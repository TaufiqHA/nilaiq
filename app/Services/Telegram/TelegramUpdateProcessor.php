<?php

namespace App\Services\Telegram;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\TelegramContext;
use App\Models\TelegramLink;
use App\Models\TelegramLinkToken;
use App\Models\User;
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
            $this->send($chatId, $this->formatter->onboarding());

            return;
        }

        $user = $link->user;

        if ($user->role !== 'mapel') {
            $this->send($chatId, $this->formatter->roleNotSupported());

            return;
        }

        if (! Str::startsWith($data, 'c:')) {
            $this->send($chatId, 'Pilihan tidak dikenal. Ketik /bantuan untuk melihat contoh pertanyaan.');

            return;
        }

        $classId = (int) Str::after($data, 'c:');
        $scope = $this->scopeFor($user);
        $class = $scope['classes']->firstWhere('id', $classId);

        if (! $class instanceof Classes) {
            $this->send($chatId, $this->formatter->cannotFindClass());

            return;
        }

        $this->rememberClass($chatId, $class);
        $this->touchLink($link);

        $this->send(
            $chatId,
            'Kelas aktif sekarang: <b>'.e($class->name).'</b>.'.PHP_EOL.PHP_EOL
            .'Contoh: "rekap kehadiran kelas ini" atau "rekap lengkap".'
        );
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
            $this->send($chatId, $this->formatter->onboarding());

            return;
        }

        $user = $link->user;

        if ($user->role !== 'mapel') {
            $this->send($chatId, $this->formatter->roleNotSupported());

            return;
        }

        $this->touchLink($link);

        $command = $this->parseCommand($text);

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

        $this->send($chatId, $this->formatter->linked($linkToken->user->name));
    }

    private function handleCommand(User $user, TelegramLink $link, int $chatId, string $command, ?string $argument): void
    {
        switch ($command) {
            case 'start':
            case 'bantuan':
            case 'help':
                $this->send($chatId, $this->formatter->help());
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

    private function dispatchIntent(int $chatId, Classes $class, TelegramIntent $intent): void
    {
        switch ($intent->type) {
            case TelegramIntent::ATTENDANCE_SUMMARY:
                $summary = $this->attendance->classAttendance($class);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceSummary($summary, $intent->full, $intent->limit)
                );
                break;

            case TelegramIntent::LOWEST_ATTENDANCE:
                $summary = $this->attendance->classAttendance($class);
                $rows = $this->attendanceRows($summary, 'percentage', false, 'alfa', true);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceRanking('Kehadiran terendah', $summary, $rows, $intent->limit)
                );
                break;

            case TelegramIntent::HIGHEST_ATTENDANCE:
                $summary = $this->attendance->classAttendance($class);
                $rows = $this->attendanceRows($summary, 'percentage', true, 'hadir', true);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceRanking('Kehadiran tertinggi', $summary, $rows, $intent->limit)
                );
                break;

            case TelegramIntent::MOST_ABSENT:
                $summary = $this->attendance->classAttendance($class);
                $rows = $this->attendanceRows($summary, 'alfa', true, 'percentage', false);
                $this->send(
                    $chatId,
                    $this->formatter->attendanceRanking('Paling sering alfa', $summary, $rows, $intent->limit)
                );
                break;

            case TelegramIntent::STUDENT_ATTENDANCE:
                $summary = $this->attendance->classAttendance($class);
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
                $this->send($chatId, $this->formatter->scoreRecap($recap, $intent->full, $intent->limit));
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

                $this->send($chatId, $this->formatter->averageRanking($title, $recap, $rows, $intent->limit));
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

                $this->send($chatId, $this->formatter->assessmentRanking($title, $recap, $rows, $intent->limit));
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
                $summary = $this->attendance->classAttendance($class);
                $this->send(
                    $chatId,
                    $this->formatter->fullRecap($recap, $summary, $intent->full, $intent->limit)
                );
                break;

            default:
                $this->send($chatId, $this->formatter->help());
        }
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
        $year = AcademicYear::getActive($user->id);

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

            if ($keyboard !== null && $index === $lastIndex) {
                $extra['reply_markup'] = $keyboard;
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
