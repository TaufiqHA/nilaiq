<?php

namespace App\Services\Telegram;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Settings;
use App\Models\Students;
use App\Models\TelegramContext;
use App\Models\TelegramLink;
use App\Models\TelegramLinkToken;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

/**
 * Processes Telegram updates for both the webhook and the polling command.
 */
class TelegramUpdateProcessor
{
    private const MAX_MESSAGES_PER_MINUTE = 20;

    public function __construct(
        private readonly TelegramClient $client,
        private readonly RuleBasedTelegramIntentParser $parser,
        private readonly AttendanceInsightService $attendance,
        private readonly ClassRecapInsightService $recaps,
        private readonly TelegramReplyFormatter $formatter,
        private readonly TelegramExportService $exports,
    ) {}

    /**
     * @param  array<string, mixed>  $update
     */
    public function process(array $update): void
    {
        $updateId = isset($update['update_id']) ? (int) $update['update_id'] : null;
        $marked = false;

        if ($updateId !== null) {
            $marked = DB::table('telegram_processed_updates')->insertOrIgnore([
                'update_id' => $updateId,
                'created_at' => now(),
            ]) > 0;

            if (! $marked) {
                return;
            }

            if (random_int(1, 100) === 1) {
                DB::table('telegram_processed_updates')
                    ->where('created_at', '<', now()->subDays(2))
                    ->delete();
            }
        }

        try {
            if (isset($update['callback_query'])) {
                $this->handleCallback($update['callback_query']);

                return;
            }

            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            }
        } catch (Throwable $e) {
            // Allow Telegram to retry the update after a transient failure.
            if ($marked && $updateId !== null) {
                DB::table('telegram_processed_updates')->where('update_id', $updateId)->delete();
            }

            Log::error('Telegram update failed: '.$e->getMessage(), [
                'update_id' => $updateId,
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleCallback(array $payload): void
    {
        $callbackId = (string) ($payload['id'] ?? '');
        $chatId = $this->extractCallbackChatId($payload);
        $data = (string) ($payload['data'] ?? '');

        if ($chatId === null) {
            return;
        }

        $link = $this->findLink($chatId);

        if ($callbackId !== '') {
            try {
                $this->client->answerCallbackQuery($callbackId);
            } catch (Throwable $e) {
                // Answering is cosmetic; never block the class selection.
                Log::warning('Telegram answerCallbackQuery failed: '.$e->getMessage());
            }
        }

        if ($link === null) {
            $this->send($chatId, $this->formatter->onboarding(), $this->formatter->onboardingKeyboard());

            return;
        }

        $user = $link->user;

        if ($user->role !== 'mapel') {
            $this->send($chatId, $this->formatter->roleNotSupported());

            return;
        }

        $scope = $this->scopeFor($user);

        if ($scope['classes']->isEmpty()) {
            $this->send($chatId, $this->formatter->noClasses());

            return;
        }

        if (preg_match('/^c:(\d+)$/', $data, $matches) === 1) {
            $class = $scope['classes']->firstWhere('id', (int) $matches[1]);

            if (! $class instanceof Classes) {
                $this->send($chatId, $this->formatter->cannotFindClass());

                return;
            }

            $this->rememberClass($chatId, $class);
            $this->touchLink($link);

            $this->send(
                $chatId,
                'Kelas aktif sekarang: <b>'.e($class->name).'</b>.'.PHP_EOL.PHP_EOL
                .'Contoh: "rekap kehadiran kelas ini" atau "rekap lengkap".',
                $this->formatter->removeKeyboard()
            );

            return;
        }

        if (preg_match('/^k:(\d+)$/', $data, $matches) === 1) {
            $selection = $this->formatter->classSelection($scope['classes'], (int) $matches[1]);
            $this->send($chatId, $selection['text'], $selection['keyboard']);

            return;
        }

        if (preg_match('/^v:([a-z]):(\d+):(\d+)$/', $data, $matches) === 1) {
            $class = $scope['classes']->firstWhere('id', (int) $matches[2]);

            if (! $class instanceof Classes) {
                $this->send($chatId, $this->formatter->cannotFindClass());

                return;
            }

            $intentType = match ($matches[1]) {
                'a' => TelegramIntent::ATTENDANCE_SUMMARY,
                'l' => TelegramIntent::LOWEST_ATTENDANCE,
                'h' => TelegramIntent::HIGHEST_ATTENDANCE,
                'm' => TelegramIntent::MOST_ABSENT,
                's' => TelegramIntent::SCORE_RECAP,
                'u' => TelegramIntent::LOWEST_AVERAGE,
                't' => TelegramIntent::HIGHEST_AVERAGE,
                'f' => TelegramIntent::FULL_RECAP,
                'd', 'g', 'p', 'z' => TelegramIntent::ASSESSMENT_RANKING,
                default => null,
            };

            if ($intentType === null) {
                $this->send($chatId, 'Pilihan tidak dikenal. Ketik /bantuan untuk melihat contoh pertanyaan.');

                return;
            }

            $assessment = match ($matches[1]) {
                'd' => TelegramIntent::ASSESSMENT_DAILY_TEST,
                'g' => TelegramIntent::ASSESSMENT_ASSIGNMENT,
                'p' => TelegramIntent::ASSESSMENT_MIDTERM,
                'z' => TelegramIntent::ASSESSMENT_FINAL,
                default => null,
            };

            $this->rememberClass($chatId, $class);
            $this->touchLink($link);

            $this->dispatchIntent(
                $chatId,
                $class,
                new TelegramIntent(
                    $intentType,
                    classId: $class->id,
                    limit: 10,
                    assessmentType: $assessment,
                    descending: true
                ),
                max(1, (int) $matches[3])
            );

            return;
        }

        if (preg_match('/^x:([asf]):(\d+)$/', $data, $matches) === 1) {
            $class = $scope['classes']->firstWhere('id', (int) $matches[2]);

            if (! $class instanceof Classes) {
                $this->send($chatId, $this->formatter->cannotFindClass());

                return;
            }

            if ($this->isRateLimited($chatId)) {
                $seconds = RateLimiter::availableIn($this->rateLimitKey($chatId));
                $this->send($chatId, $this->formatter->rateLimited($seconds));

                return;
            }

            $this->touchLink($link);

            $this->sendExport($chatId, $class, match ($matches[1]) {
                'a' => 'attendance',
                'f' => 'full',
                default => 'scores',
            });

            return;
        }

        $this->send($chatId, 'Pilihan tidak dikenal. Ketik /bantuan untuk melihat contoh pertanyaan.');
    }

    /**
     * @param  array<string, mixed>  $message
     */
    private function handleMessage(array $message): void
    {
        $chat = $message['chat'] ?? [];
        $chatId = isset($chat['id']) ? (int) $chat['id'] : null;

        if ($chatId === null) {
            return;
        }

        if (($chat['type'] ?? 'private') !== 'private') {
            $this->send($chatId, $this->formatter->privateChatOnly());

            return;
        }

        $text = trim((string) ($message['text'] ?? ''));

        if ($text === '') {
            return;
        }

        if (preg_match('/^\/start(?:@\w+)?(?:\s+(\S+))?$/i', $text, $matches) === 1) {
            $token = $matches[1] ?? null;

            if ($token !== null && $token !== '') {
                $this->linkChat($chatId, $token, $message);

                return;
            }
        }

        $link = $this->findLink($chatId);

        if ($link === null) {
            $this->send($chatId, $this->formatter->onboarding(), $this->formatter->onboardingKeyboard());

            return;
        }

        $user = $link->user;

        if ($user->role !== 'mapel') {
            $this->send($chatId, $this->formatter->roleNotSupported());

            return;
        }

        $this->touchLink($link);

        $text = $this->expandShortcut($text);
        $command = $this->parseCommand($text);

        $freeCommands = ['start', 'bantuan', 'help', 'ping', 'putuskan'];

        if (! $user->hasAppAccess() && ($command === null || ! in_array($command['name'], $freeCommands, true))) {
            $this->send(
                $chatId,
                'Masa trial atau langganan Anda sudah berakhir. Perpanjang langganan di '.route('billing.index').' untuk melanjutkan penggunaan bot.'
            );

            return;
        }

        if ($command !== null) {
            $this->handleCommand($user, $link, $chatId, $command['name'], $command['argument']);

            return;
        }

        if ($this->isRateLimited($chatId)) {
            $seconds = RateLimiter::availableIn($this->rateLimitKey($chatId));
            $this->send($chatId, $this->formatter->rateLimited($seconds));

            return;
        }

        $this->answerQuery($user, $chatId, $text);
    }

    private function linkChat(int $chatId, string $token, array $message): void
    {
        $tokenHash = hash('sha256', $token);

        $linkToken = TelegramLinkToken::where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->with('user')
            ->first();

        if ($linkToken === null || $linkToken->user === null) {
            $this->send(
                $chatId,
                'Kode penghubung tidak valid atau sudah kedaluwarsa. Buat kode baru dari halaman Profil di website NilaiQ.'
            );

            return;
        }

        if ($linkToken->user->role !== 'mapel') {
            $this->send($chatId, $this->formatter->roleNotSupported());

            return;
        }

        $existingForChat = TelegramLink::where('chat_id', $chatId)->first();

        if ($existingForChat !== null && $existingForChat->user_id !== $linkToken->user_id) {
            $this->send(
                $chatId,
                'Chat Telegram ini sudah terhubung ke akun NilaiQ lain. Ketik /putuskan terlebih dahulu jika ingin menggantinya.'
            );

            return;
        }

        TelegramLink::where('user_id', $linkToken->user_id)
            ->orWhere('chat_id', $chatId)
            ->delete();

        TelegramLink::create([
            'user_id' => $linkToken->user_id,
            'chat_id' => $chatId,
            'telegram_username' => $message['from']['username'] ?? null,
            'linked_at' => now(),
            'last_active_at' => now(),
        ]);

        $linkToken->update(['used_at' => now()]);

        $this->send(
            $chatId,
            $this->formatter->linked($linkToken->user->name),
            $this->formatter->removeKeyboard()
        );
    }

    private function handleCommand(User $user, TelegramLink $link, int $chatId, string $command, ?string $argument): void
    {
        switch ($command) {
            case 'start':
            case 'bantuan':
            case 'help':
                $this->send($chatId, $this->formatter->help(), $this->formatter->removeKeyboard());
                break;

            case 'ping':
                $this->send(
                    $chatId,
                    $this->formatter->pong((string) config('services.telegram.username'))
                );
                break;

            case 'cari':
                $term = trim((string) $argument);

                if ($term === '') {
                    $this->send($chatId, 'Ketik /cari diikuti nama siswa, misalnya: /cari Budi');
                    break;
                }

                $this->searchStudents($this->scopeFor($user)['classes'], $chatId, $term);
                break;

            case 'ekspor':
                if ($this->isRateLimited($chatId)) {
                    $seconds = RateLimiter::availableIn($this->rateLimitKey($chatId));
                    $this->send($chatId, $this->formatter->rateLimited($seconds));
                    break;
                }

                $this->handleExportCommand($user, $chatId, $argument);
                break;

            case 'status':
                $scope = $this->scopeFor($user);
                $context = $this->contextFor($chatId, $scope['classes']);

                $this->send($chatId, $this->formatter->status(
                    $user->name,
                    'Guru Mapel',
                    $this->formatter->className($scope['classes'], $context?->class_id),
                    $link->telegram_username,
                ));
                break;

            case 'putuskan':
                TelegramLink::where('user_id', $user->id)->delete();
                TelegramContext::where('chat_id', $chatId)->delete();
                $this->send($chatId, $this->formatter->unlinked());
                break;

            case 'kelas':
                $scope = $this->scopeFor($user);

                if ($scope['classes']->isEmpty()) {
                    $this->send($chatId, $this->formatter->noClasses());
                    break;
                }

                if ($argument !== null && $argument !== '') {
                    $class = $this->parser->findClass($argument, $scope['classes']);

                    if (! $class instanceof Classes) {
                        $this->send($chatId, $this->formatter->cannotFindClass());
                        break;
                    }

                    $this->rememberClass($chatId, $class);
                    $this->send(
                        $chatId,
                        'Kelas aktif sekarang: <b>'.e($class->name).'</b>.'.PHP_EOL.PHP_EOL
                        .'Contoh: "kehadiran terendah di kelas ini".'
                    );
                    break;
                }

                $context = $this->contextFor($chatId, $scope['classes']);
                $selection = $this->formatter->classSelection($scope['classes'], $context?->class_id);
                $this->send($chatId, $selection['text'], $selection['keyboard']);
                break;

            case 'rekap':
                $scope = $this->scopeFor($user);

                if ($scope['classes']->isEmpty()) {
                    $this->send($chatId, $this->formatter->noClasses());
                    break;
                }

                if ($argument !== null && $argument !== '') {
                    $class = $this->parser->findClass($argument, $scope['classes']);

                    if (! $class instanceof Classes) {
                        $this->send($chatId, $this->formatter->cannotFindClass());
                        break;
                    }

                    $this->rememberClass($chatId, $class);
                }

                $this->answerQuery($user, $chatId, 'rekap lengkap');
                break;

            default:
                $this->send(
                    $chatId,
                    'Perintah tidak dikenal. Ketik /bantuan untuk melihat daftar perintah dan contoh pertanyaan.'
                );
        }
    }

    private function answerQuery(User $user, int $chatId, string $text): void
    {
        $scope = $this->scopeFor($user);

        if ($scope['classes']->isEmpty()) {
            $this->send($chatId, $this->formatter->noClasses());

            return;
        }

        if ($this->hasUnknownClassReference($text, $scope['classes'])) {
            $this->send($chatId, $this->formatter->cannotFindClass());

            return;
        }

        $context = $this->contextFor($chatId, $scope['classes']);
        $intent = $this->parser->parse($text, $scope['classes'], $context?->class_id);

        if ($intent->type === TelegramIntent::HELP) {
            $this->send($chatId, $this->formatter->help());

            return;
        }

        if ($intent->type === TelegramIntent::LIST_CLASSES) {
            $selection = $this->formatter->classSelection($scope['classes'], $context?->class_id);
            $this->send($chatId, $selection['text'], $selection['keyboard']);

            return;
        }

        if ($intent->type === TelegramIntent::SEARCH_STUDENT) {
            $this->searchStudents($scope['classes'], $chatId, (string) $intent->searchTerm);

            return;
        }

        if ($intent->needsClass() && $intent->classId === null) {
            $selection = $this->formatter->classSelection($scope['classes'], $context?->class_id);
            $this->send(
                $chatId,
                '<b>Kelas mana yang dimaksud?</b>'.PHP_EOL.PHP_EOL
                .'Pilih kelas di bawah, atau tulis misalnya: "kehadiran terendah di kelas 7A".',
                $selection['keyboard']
            );

            return;
        }

        $class = $intent->classId !== null
            ? $scope['classes']->firstWhere('id', $intent->classId)
            : null;

        if (! $class instanceof Classes) {
            $this->send($chatId, $this->formatter->cannotFindClass());

            return;
        }

        $this->rememberClass($chatId, $class);

        $this->dispatchIntent($chatId, $class, $intent);
    }

    private function dispatchIntent(int $chatId, Classes $class, TelegramIntent $intent, int $page = 1): void
    {
        [$from, $to, $periodLabel] = $this->periodRange($intent->period);
        $perPage = max(1, $intent->limit);
        $offset = max(0, ($page - 1) * $perPage);

        switch ($intent->type) {
            case TelegramIntent::ATTENDANCE_SUMMARY:
                $summary = $this->attendance->classAttendance($class, $from, $to);
                $total = count($summary['rows']);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceSummary(
                        $summary,
                        $intent->full,
                        $perPage,
                        $offset,
                        $periodLabel
                    ),
                    $intent->full ? null : $this->listKeyboard('a', 'attendance', $class, $page, $total, $perPage)
                );
                break;

            case TelegramIntent::LOWEST_ATTENDANCE:
                $summary = $this->attendance->classAttendance($class, $from, $to);
                $rows = $this->attendanceRows($summary, 'percentage', false, 'alfa', true);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceRanking(
                        'Kehadiran terendah'.$this->periodSuffix($periodLabel),
                        $summary,
                        $rows,
                        $perPage,
                        $offset
                    ),
                    $this->listKeyboard('l', 'attendance', $class, $page, count($rows), $perPage)
                );
                break;

            case TelegramIntent::HIGHEST_ATTENDANCE:
                $summary = $this->attendance->classAttendance($class, $from, $to);
                $rows = $this->attendanceRows($summary, 'percentage', true, 'hadir', true);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceRanking(
                        'Kehadiran tertinggi'.$this->periodSuffix($periodLabel),
                        $summary,
                        $rows,
                        $perPage,
                        $offset
                    ),
                    $this->listKeyboard('h', 'attendance', $class, $page, count($rows), $perPage)
                );
                break;

            case TelegramIntent::MOST_ABSENT:
                $summary = $this->attendance->classAttendance($class, $from, $to);
                $rows = $this->attendanceRows($summary, 'alfa', true, 'percentage', false);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceRanking(
                        'Paling sering alfa'.$this->periodSuffix($periodLabel),
                        $summary,
                        $rows,
                        $perPage,
                        $offset
                    ),
                    $this->listKeyboard('m', 'attendance', $class, $page, count($rows), $perPage)
                );
                break;

            case TelegramIntent::STUDENT_ATTENDANCE:
                $summary = $this->attendance->classAttendance($class, $from, $to);
                $row = $intent->studentId !== null
                    ? $this->attendance->studentRow($summary, $intent->studentId)
                    : null;

                if ($row === null) {
                    $this->send($chatId, $this->formatter->cannotFindStudent());
                    break;
                }

                $this->send(
                    $chatId,
                    $this->formatter->studentAttendance($row, $class->name, $summary['meeting_count'])
                );
                break;

            case TelegramIntent::SCORE_RECAP:
                $recap = $this->recaps->classRecap($class);
                $this->send(
                    $chatId,
                    $this->formatter->scoreRecap($recap, $intent->full, $perPage, $offset),
                    $intent->full ? null : $this->listKeyboard('s', 'scores', $class, $page, count($recap['rows']), $perPage)
                );
                break;

            case TelegramIntent::LOWEST_AVERAGE:
            case TelegramIntent::HIGHEST_AVERAGE:
                $recap = $this->recaps->classRecap($class);
                $rows = array_values(array_filter(
                    $recap['rows'],
                    fn (array $row): bool => $row['score_count'] > 0
                ));
                usort(
                    $rows,
                    fn (array $a, array $b): int => $intent->type === TelegramIntent::LOWEST_AVERAGE
                        ? $a['rata_rata'] <=> $b['rata_rata']
                        : $b['rata_rata'] <=> $a['rata_rata']
                );

                $title = $intent->type === TelegramIntent::LOWEST_AVERAGE
                    ? 'Rata-rata nilai terendah'
                    : 'Rata-rata nilai tertinggi';

                $this->send(
                    $chatId,
                    $this->formatter->averageRanking($title, $recap, $rows, $perPage, $offset),
                    $this->listKeyboard(
                        $intent->type === TelegramIntent::LOWEST_AVERAGE ? 'u' : 't',
                        'scores',
                        $class,
                        $page,
                        count($rows),
                        $perPage
                    )
                );
                break;

            case TelegramIntent::ASSESSMENT_RANKING:
                $assessment = $intent->assessmentType ?? TelegramIntent::ASSESSMENT_DAILY_TEST;
                $recap = $this->recaps->classRecap($class);
                $rows = $this->recaps->assessmentRanking($class, $assessment);

                if (! $intent->descending) {
                    $rows = array_reverse($rows);
                }

                $label = $this->assessmentLabel($assessment);
                $title = $label.' '.($intent->descending ? 'tertinggi' : 'terendah');
                $viewCode = match ($assessment) {
                    TelegramIntent::ASSESSMENT_ASSIGNMENT => 'g',
                    TelegramIntent::ASSESSMENT_MIDTERM => 'p',
                    TelegramIntent::ASSESSMENT_FINAL => 'z',
                    default => 'd',
                };

                $this->send(
                    $chatId,
                    $this->formatter->assessmentRanking($title, $recap, $rows, $perPage, $offset),
                    $this->listKeyboard($viewCode, 'scores', $class, $page, count($rows), $perPage)
                );
                break;

            case TelegramIntent::STUDENT_SCORE:
                $recap = $this->recaps->classRecap($class);
                $row = $intent->studentId !== null
                    ? $this->recaps->studentRow($recap, $intent->studentId)
                    : null;

                if ($row === null) {
                    $this->send($chatId, $this->formatter->cannotFindStudent());
                    break;
                }

                $this->send(
                    $chatId,
                    $this->formatter->studentScore($recap, $row, $intent->assessmentType)
                );
                break;

            case TelegramIntent::FULL_RECAP:
                $recap = $this->recaps->classRecap($class);
                $summary = $this->attendance->classAttendance($class, $from, $to);
                $this->send(
                    $chatId,
                    $this->formatter->fullRecap($recap, $summary, $intent->full, $perPage, $offset),
                    $intent->full ? null : $this->listKeyboard('f', 'full', $class, $page, count($recap['rows']), $perPage)
                );
                break;

            case TelegramIntent::BELOW_KKM:
                $this->sendBelowKkm($chatId, $class, $intent);
                break;

            case TelegramIntent::MISSING_SCORES:
                $this->sendMissingScores($chatId, $class, $intent);
                break;

            case TelegramIntent::AT_RISK:
                $this->sendAtRisk($chatId, $class, $intent);
                break;

            case TelegramIntent::COMPARE_CLASSES:
                $secondary = Classes::where('academic_year_id', $class->academic_year_id)
                    ->find($intent->secondaryClassId);

                if (! $secondary instanceof Classes) {
                    $this->send($chatId, $this->formatter->cannotFindClass());
                    break;
                }

                $this->send(
                    $chatId,
                    $this->formatter->compareClasses(
                        $this->classOverview($class),
                        $this->classOverview($secondary)
                    )
                );
                break;

            case TelegramIntent::EXPORT_ATTENDANCE:
                $this->sendExport($chatId, $class, 'attendance');
                break;

            case TelegramIntent::EXPORT_SCORES:
                $this->sendExport($chatId, $class, 'scores');
                break;

            case TelegramIntent::EXPORT_FULL:
                $this->sendExport($chatId, $class, 'full');
                break;

            default:
                $this->send($chatId, $this->formatter->help(), $this->formatter->removeKeyboard());
        }
    }

    /**
     * @return array{0: Carbon|null, 1: Carbon|null, 2: string|null}
     */
    private function periodRange(?string $period): array
    {
        if ($period === 'this_month') {
            $now = Carbon::now();

            return [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                'Bulan ini',
            ];
        }

        if ($period === 'last_month') {
            $lastMonth = Carbon::now()->subMonthNoOverflow();

            return [
                $lastMonth->copy()->startOfMonth(),
                $lastMonth->copy()->endOfMonth(),
                'Bulan lalu',
            ];
        }

        return [null, null, null];
    }

    private function periodSuffix(?string $periodLabel): string
    {
        return $periodLabel !== null ? ' ('.$periodLabel.')' : '';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function listKeyboard(
        string $view,
        string $exportKind,
        Classes $class,
        int $page,
        int $total,
        int $perPage,
    ): ?array {
        if ($total <= 0) {
            return null;
        }

        $rows = [];
        $navigation = [];

        if ($page > 1) {
            $navigation[] = [
                'text' => '« Sebelumnya',
                'callback_data' => 'v:'.$view.':'.$class->id.':'.($page - 1),
            ];
        }

        if ($perPage >= 10 && $page * $perPage < $total) {
            $navigation[] = [
                'text' => 'Berikutnya »',
                'callback_data' => 'v:'.$view.':'.$class->id.':'.($page + 1),
            ];
        }

        if ($navigation !== []) {
            $rows[] = $navigation;
        }

        $exportCode = match ($exportKind) {
            'attendance' => 'a',
            'full' => 'f',
            default => 's',
        };

        $rows[] = [[
            'text' => '📊 Ekspor Excel',
            'callback_data' => 'x:'.$exportCode.':'.$class->id,
        ]];

        $rows[] = [
            [
                'text' => '🔄 Ganti kelas',
                'callback_data' => 'k:'.$class->id,
            ],
            [
                'text' => '🌐 Buka website',
                'url' => route($exportKind === 'attendance' ? 'rekap-absensi.index' : 'rekap.index'),
            ],
        ];

        return ['inline_keyboard' => $rows];
    }

    private function kkm(Classes $class): ?float
    {
        $minimumScore = Settings::query()
            ->where('user_id', $class->academicYear?->user_id)
            ->value('minimum_score');

        return $minimumScore !== null ? (float) $minimumScore : null;
    }

    private function sendBelowKkm(int $chatId, Classes $class, TelegramIntent $intent): void
    {
        $kkm = $this->kkm($class);

        if ($kkm === null) {
            $this->send($chatId, 'KKM belum diatur. Atur nilai KKM di menu Pengaturan website NilaiQ terlebih dahulu.');

            return;
        }

        $recap = $this->recaps->classRecap($class);
        $rows = [];

        foreach ($recap['rows'] as $row) {
            $score = $intent->assessmentType !== null
                ? ($row['type_scores'][$intent->assessmentType]['average'] ?? null)
                : $row['final_score'];

            if ($score !== null && (float) $score < $kkm) {
                $rows[] = [
                    'student' => $row['student'],
                    'score' => (float) $score,
                ];
            }
        }

        usort($rows, fn (array $a, array $b): int => $a['score'] <=> $b['score']);

        $this->send(
            $chatId,
            $this->formatter->belowKkm(
                $class,
                $rows,
                $kkm,
                $intent->limit,
                $intent->assessmentType !== null ? $this->assessmentLabel($intent->assessmentType) : null
            )
        );
    }

    private function sendMissingScores(int $chatId, Classes $class, TelegramIntent $intent): void
    {
        $recap = $this->recaps->classRecap($class);
        $counts = $recap['assessment_counts'];
        $types = $intent->assessmentType !== null
            ? [$intent->assessmentType]
            : array_keys($counts);
        $rows = [];

        foreach ($recap['rows'] as $row) {
            $missing = [];
            $totalMissing = 0;

            foreach ($types as $type) {
                $expected = $counts[$type] ?? 0;

                if ($expected <= 0) {
                    continue;
                }

                $recorded = $row['type_scores'][$type]['count'] ?? 0;
                $missingCount = max(0, $expected - $recorded);

                if ($missingCount > 0) {
                    $missing[$type] = $missingCount;
                    $totalMissing += $missingCount;
                }
            }

            if ($totalMissing > 0) {
                $rows[] = [
                    'student' => $row['student'],
                    'missing' => $missing,
                    'total' => $totalMissing,
                ];
            }
        }

        usort($rows, fn (array $a, array $b): int => $b['total'] <=> $a['total']);

        $this->send(
            $chatId,
            $this->formatter->missingScores(
                $class,
                $rows,
                $counts,
                $intent->limit,
                $intent->assessmentType !== null ? $this->assessmentLabel($intent->assessmentType) : null
            )
        );
    }

    private function sendAtRisk(int $chatId, Classes $class, TelegramIntent $intent): void
    {
        $kkm = $this->kkm($class);
        $summary = $this->attendance->classAttendance($class);
        $recap = $this->recaps->classRecap($class);
        $attendanceRows = collect($summary['rows'])
            ->keyBy(fn (array $row): int => $row['student']->id);
        $rows = [];

        foreach ($recap['rows'] as $row) {
            $attendanceRow = $attendanceRows->get($row['student']->id);
            $percentage = $attendanceRow['percentage'] ?? null;
            $alfa = $attendanceRow['alfa'] ?? 0;
            $score = $row['final_score'] ?? round($row['rata_rata']);
            $reasons = [];

            if ($percentage !== null && $percentage < 75) {
                $reasons[] = 'kehadiran rendah';
            } elseif ($alfa >= 3) {
                $reasons[] = 'sering alfa';
            }

            if ($kkm !== null && $score !== null && (float) $score < $kkm) {
                $reasons[] = 'nilai di bawah KKM';
            }

            if ($reasons === []) {
                continue;
            }

            $rows[] = [
                'student' => $row['student'],
                'percentage' => $percentage,
                'average' => $row['rata_rata'],
                'final_score' => $row['final_score'],
                'reasons' => $reasons,
            ];
        }

        usort($rows, function (array $a, array $b): int {
            $countComparison = count($b['reasons']) <=> count($a['reasons']);

            if ($countComparison !== 0) {
                return $countComparison;
            }

            return ($a['percentage'] ?? 101) <=> ($b['percentage'] ?? 101);
        });

        $this->send($chatId, $this->formatter->atRisk($class, $rows, $kkm, $intent->limit));
    }

    /**
     * @return array<string, mixed>
     */
    private function classOverview(Classes $class): array
    {
        $summary = $this->attendance->classAttendance($class);
        $recap = $this->recaps->classRecap($class);
        $kkm = $this->kkm($class);
        $scoredRows = array_values(array_filter(
            $recap['rows'],
            fn (array $row): bool => $row['score_count'] > 0
        ));
        $averageScore = $scoredRows !== []
            ? round(array_sum(array_column($scoredRows, 'rata_rata')) / count($scoredRows), 2)
            : null;
        $belowKkm = 0;

        if ($kkm !== null) {
            foreach ($scoredRows as $row) {
                if ($row['final_score'] !== null && (float) $row['final_score'] < $kkm) {
                    $belowKkm++;
                }
            }
        }

        return [
            'class' => $class,
            'students' => count($summary['rows']),
            'average_percentage' => $summary['average_percentage'],
            'average_score' => $averageScore,
            'below_kkm' => $belowKkm,
        ];
    }

    /**
     * @param  Collection<int, Classes>  $classes
     */
    private function searchStudents(Collection $classes, int $chatId, string $term): void
    {
        $term = trim($term);

        if ($term === '') {
            $this->send($chatId, 'Ketik /cari diikuti nama siswa, misalnya: /cari Budi');

            return;
        }

        if ($classes->isEmpty()) {
            $this->send($chatId, $this->formatter->noClasses());

            return;
        }

        $classById = $classes->keyBy('id');
        $students = Students::whereIn('class_id', $classes->pluck('id'))
            ->where('status', 'ACTIVE')
            ->where('name', 'like', '%'.$term.'%')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $matches = [];

        foreach ($students as $student) {
            $class = $classById->get($student->class_id);

            if ($class instanceof Classes) {
                $matches[] = ['student' => $student, 'class' => $class];
            }
        }

        $this->send($chatId, $this->formatter->searchResults($term, $matches));
    }

    private function handleExportCommand(User $user, int $chatId, ?string $argument): void
    {
        $scope = $this->scopeFor($user);

        if ($scope['classes']->isEmpty()) {
            $this->send($chatId, $this->formatter->noClasses());

            return;
        }

        $argument = trim((string) $argument);
        $kind = 'scores';
        $classArgument = null;

        if ($argument !== '') {
            $parts = preg_split('/\s+/', $argument, 2) ?: [$argument];
            $first = Str::lower($parts[0]);

            if (in_array($first, ['absensi', 'kehadiran', 'nilai', 'lengkap', 'semua'], true)) {
                $kind = match ($first) {
                    'absensi', 'kehadiran' => 'attendance',
                    'lengkap', 'semua' => 'full',
                    default => 'scores',
                };
                $classArgument = $parts[1] ?? null;
            } else {
                $classArgument = $argument;
            }
        }

        $context = $this->contextFor($chatId, $scope['classes']);
        $class = null;

        if ($classArgument !== null && $classArgument !== '') {
            $class = $this->parser->findClass($classArgument, $scope['classes']);

            if (! $class instanceof Classes) {
                $this->send($chatId, $this->formatter->cannotFindClass());

                return;
            }
        } elseif ($context?->class_id !== null) {
            $class = $scope['classes']->firstWhere('id', $context->class_id);
        } elseif ($scope['classes']->count() === 1) {
            $class = $scope['classes']->first();
        }

        if (! $class instanceof Classes) {
            $selection = $this->formatter->classSelection($scope['classes'], $context?->class_id);
            $this->send(
                $chatId,
                '<b>Kelas mana yang ingin diekspor?</b>'.PHP_EOL.PHP_EOL
                .'Pilih kelas di bawah, atau tulis misalnya: /ekspor absensi 7A',
                $selection['keyboard']
            );

            return;
        }

        $this->sendExport($chatId, $class, $kind);
    }

    private function sendExport(int $chatId, Classes $class, string $kind): void
    {
        $file = null;

        try {
            $file = match ($kind) {
                'attendance' => $this->exports->attendance(
                    $class,
                    $this->attendance->classAttendance($class)
                ),
                'full' => $this->exports->full(
                    $class,
                    $this->recaps->classRecap($class),
                    $this->attendance->classAttendance($class)
                ),
                default => $this->exports->scores(
                    $class,
                    $this->recaps->classRecap($class)
                ),
            };

            $this->client->sendDocument(
                $chatId,
                $file['path'],
                $file['filename'],
                $this->formatter->exportCaption($class, $kind)
            );
        } catch (Throwable $e) {
            Log::error('Telegram export failed: '.$e->getMessage(), [
                'class_id' => $class->id,
                'kind' => $kind,
                'exception' => $e,
            ]);

            $this->send($chatId, 'Gagal membuat file ekspor. Coba lagi beberapa saat lagi.');
        } finally {
            if ($file !== null && is_file($file['path'])) {
                @unlink($file['path']);
            }
        }
    }

    private function expandShortcut(string $text): string
    {
        return match (Str::lower(trim($text))) {
            'rekap' => '/rekap',
            'kehadiran' => 'rekap kehadiran',
            'nilai' => 'rekap nilai',
            'kelas' => '/kelas',
            'bantuan' => '/bantuan',
            default => $text,
        };
    }

    /**
     * @param  array<string, mixed>  $summary
     * @return array<int, array<string, mixed>>
     */
    private function attendanceRows(
        array $summary,
        string $primaryField,
        bool $primaryDescending,
        ?string $secondaryField = null,
        ?bool $secondaryDescending = null,
    ): array {
        $rows = array_values(array_filter(
            $summary['rows'],
            fn (array $row): bool => $row['recorded'] > 0 && $row['percentage'] !== null
        ));

        usort($rows, function (array $a, array $b) use (
            $primaryField,
            $primaryDescending,
            $secondaryField,
            $secondaryDescending,
        ): int {
            $aPrimary = $a[$primaryField] ?? 0;
            $bPrimary = $b[$primaryField] ?? 0;
            $result = $primaryDescending
                ? $bPrimary <=> $aPrimary
                : $aPrimary <=> $bPrimary;

            if ($result !== 0 || $secondaryField === null) {
                return $result;
            }

            $aSecondary = $a[$secondaryField] ?? 0;
            $bSecondary = $b[$secondaryField] ?? 0;

            return ($secondaryDescending ?? true)
                ? $bSecondary <=> $aSecondary
                : $aSecondary <=> $bSecondary;
        });

        return $rows;
    }

    /**
     * Detect an explicit class name that does not belong to the user.
     *
     * @param  Collection<int, Classes>  $classes
     */
    private function hasUnknownClassReference(string $text, Collection $classes): bool
    {
        $normalized = Str::lower($text);
        $normalized = preg_replace('/[^a-z0-9]+/u', ' ', $normalized) ?? '';
        $normalized = trim(preg_replace('/\s+/', ' ', $normalized) ?? '');
        $padded = ' '.$normalized.' ';

        if (! str_contains($padded, ' kelas ')) {
            return false;
        }

        foreach ([
            'kelas ini', 'kelas itu', 'kelas tersebut', 'kelas apa',
            'daftar kelas', 'kelas saya', 'kelas yang mana',
        ] as $contextPhrase) {
            if (str_contains($padded, ' '.$contextPhrase.' ')) {
                return false;
            }
        }

        return $this->parser->findClass($text, $classes) === null;
    }

    private function assessmentLabel(string $type): string
    {
        return match ($type) {
            TelegramIntent::ASSESSMENT_MIDTERM => 'Nilai PTS',
            TelegramIntent::ASSESSMENT_FINAL => 'Nilai PAS',
            TelegramIntent::ASSESSMENT_ASSIGNMENT => 'Nilai Tugas',
            default => 'Nilai Ulangan Harian',
        };
    }

    /**
     * @return array{year: AcademicYear|null, classes: Collection<int, Classes>}
     */
    private function scopeFor(User $user): array
    {
        $year = AcademicYear::getActive($user->id)
            ?? AcademicYear::ownedBy($user->id)->latest()->first();

        if ($year === null) {
            return ['year' => null, 'classes' => collect()];
        }

        $classes = Classes::where('academic_year_id', $year->id)
            ->with(['students' => fn ($query) => $query->where('status', 'ACTIVE')->orderBy('name')])
            ->orderBy('name')
            ->get();

        return ['year' => $year, 'classes' => $classes];
    }

    /**
     * @param  Collection<int, Classes>  $classes
     */
    private function contextFor(int $chatId, Collection $classes): ?TelegramContext
    {
        $context = TelegramContext::where('chat_id', $chatId)->first();

        if ($context !== null && $context->class_id !== null && $classes->firstWhere('id', $context->class_id) === null) {
            $context->update(['class_id' => null]);
            $context->class_id = null;
        }

        return $context;
    }

    private function rememberClass(int $chatId, Classes $class): void
    {
        TelegramContext::updateOrCreate(
            ['chat_id' => $chatId],
            ['class_id' => $class->id]
        );
    }

    private function touchLink(TelegramLink $link): void
    {
        $link->forceFill(['last_active_at' => now()])->save();
    }

    private function findLink(int $chatId): ?TelegramLink
    {
        return TelegramLink::with('user')->where('chat_id', $chatId)->first();
    }

    /**
     * @return array{name: string, argument: string|null}|null
     */
    private function parseCommand(string $text): ?array
    {
        if (preg_match('/^\/([a-zA-Z0-9_]+)(?:@\w+)?(?:\s+(.*))?$/s', $text, $matches) !== 1) {
            return null;
        }

        $argument = isset($matches[2]) ? trim($matches[2]) : null;

        return [
            'name' => Str::lower($matches[1]),
            'argument' => $argument !== '' ? $argument : null,
        ];
    }

    private function rateLimitKey(int $chatId): string
    {
        return 'telegram-query:'.$chatId;
    }

    private function isRateLimited(int $chatId): bool
    {
        $key = $this->rateLimitKey($chatId);

        if (RateLimiter::tooManyAttempts($key, self::MAX_MESSAGES_PER_MINUTE)) {
            return true;
        }

        RateLimiter::hit($key, 60);

        return false;
    }

    /**
     * @param  array<string, mixed>|null  $keyboard
     */
    private function send(int $chatId, string $text, ?array $keyboard = null): void
    {
        $chunks = $this->formatter->chunk($text);
        $lastIndex = count($chunks) - 1;

        foreach ($chunks as $index => $chunk) {
            $extra = [];

            if ($index === $lastIndex) {
                $extra['reply_markup'] = $keyboard ?? $this->formatter->removeKeyboard();
            }

            $this->client->sendMessage($chatId, $chunk, $extra);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function extractCallbackChatId(array $payload): ?int
    {
        $message = $payload['message'] ?? null;

        if (! is_array($message)) {
            return null;
        }

        $chatId = $message['chat']['id'] ?? null;

        return is_numeric($chatId) ? (int) $chatId : null;
    }
}
