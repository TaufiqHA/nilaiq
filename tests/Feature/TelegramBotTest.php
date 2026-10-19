<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\Settings;
use App\Models\Students;
use App\Models\TelegramContext;
use App\Models\TelegramLink;
use App\Models\TelegramLinkToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class TelegramBotTest extends TestCase
{
    use RefreshDatabase;

    private const CHAT_ID = 555001;

    private const SECRET = 'test-telegram-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.telegram.token' => 'test-bot-token',
            'services.telegram.username' => 'nilaiq_test_bot',
            'services.telegram.webhook_secret' => self::SECRET,
        ]);

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true, 'result' => []], 200),
        ]);
    }

    public function test_webhook_rejects_invalid_secret(): void
    {
        $this->postJson('/api/telegram/webhook', ['update_id' => 1])->assertStatus(403);
    }

    public function test_unlinked_chat_receives_onboarding(): void
    {
        $this->sendMessageUpdate('halo', 1)->assertOk();

        $this->assertTrue($this->hasSentText('belum terhubung'));
    }

    public function test_linking_via_profile_token_connects_chat(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);

        $response = $this->actingAs($user)->post(route('profile.telegram.link'));
        $response->assertRedirect();
        $response->assertSessionHas('telegram_link_url');

        $url = (string) $response->getSession()->get('telegram_link_url');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $token = (string) ($query['start'] ?? '');

        $this->assertNotSame('', $token);

        $this->sendMessageUpdate('/start '.$token, 2)->assertOk();

        $this->assertDatabaseHas('telegram_links', [
            'user_id' => $user->id,
            'chat_id' => self::CHAT_ID,
        ]);
        $this->assertDatabaseMissing('telegram_link_tokens', [
            'user_id' => $user->id,
            'used_at' => null,
        ]);
        $this->assertTrue($this->hasSentText('Berhasil terhubung'));
    }

    public function test_expired_linking_token_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);

        TelegramLinkToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', 'expired-token'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->sendMessageUpdate('/start expired-token', 3)->assertOk();

        $this->assertDatabaseMissing('telegram_links', ['user_id' => $user->id]);
        $this->assertTrue($this->hasSentText('tidak valid'));
    }

    public function test_lowest_attendance_query_uses_database(): void
    {
        $user = $this->createMapelWithClass();

        $this->link($user);
        $this->sendMessageUpdate('siswa yang kehadirannya paling rendah di kelas VII A', 4)->assertOk();

        $this->assertTrue($this->hasSentText('Budi Santoso'));
        $this->assertTrue($this->hasSentText('33,3%'));
    }

    public function test_assessment_ranking_query_uses_database(): void
    {
        $user = $this->createMapelWithClass();

        $this->link($user);
        $this->sendMessageUpdate('nilai UH terendah di kelas VII A', 5)->assertOk();

        $this->assertTrue($this->hasSentText('Budi Santoso'));
        $this->assertTrue($this->hasSentText('50,0'));
    }

    public function test_full_recap_combines_attendance_and_grades(): void
    {
        $user = $this->createMapelWithClass();

        $this->link($user);
        $this->sendMessageUpdate('rekap lengkap kelas VII A', 6)->assertOk();

        $this->assertTrue($this->hasSentText('Rekap Lengkap'));
        $this->assertTrue($this->hasSentText('Rata-rata kehadiran'));
        $this->assertTrue($this->hasSentText('Budi Santoso'));
    }

    public function test_student_can_be_selected_with_callback_and_kelas_ini(): void
    {
        $user = $this->createMapelWithClass();
        $class = Classes::where('name', 'VII A')->firstOrFail();

        $this->link($user);
        $this->sendCallbackUpdate('c:'.$class->id, 7)->assertOk();

        $this->assertDatabaseHas('telegram_contexts', [
            'chat_id' => self::CHAT_ID,
            'class_id' => $class->id,
        ]);
        $this->assertTrue($this->hasSentText('Kelas aktif sekarang'));

        $this->sendMessageUpdate('rekap kehadiran kelas ini', 8)->assertOk();
        $this->assertTrue($this->hasSentText('Rekap Kehadiran'));
    }

    public function test_cannot_access_another_teachers_class(): void
    {
        $user = $this->createMapelWithClass();

        $otherUser = $this->createMapelWithClass('VIII B', 'Siti Aminah');

        $this->link($user);
        $this->sendMessageUpdate('kehadiran terendah di kelas VIII B', 9)->assertOk();

        $this->assertTrue($this->hasSentText('tidak ditemukan'));
        $this->assertFalse($this->hasSentText('Siti Aminah'));
        $this->assertNotSame($user->id, $otherUser->id);
    }

    public function test_non_mapel_role_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $this->link($user);

        $this->sendMessageUpdate('rekap kehadiran kelas VII A', 10)->assertOk();

        $this->assertTrue($this->hasSentText('hanya tersedia untuk akun Guru Mapel'));
    }

    public function test_duplicate_update_is_ignored(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);

        $update = $this->messageUpdate('kehadiran terendah di kelas VII A', 11);

        $this->postUpdate($update)->assertOk();
        $this->postUpdate($update)->assertOk();

        $this->assertSame(1, $this->sentMessageCount());
    }

    public function test_rate_limit_blocks_flooding(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);

        for ($i = 0; $i < 21; $i++) {
            $this->sendMessageUpdate('kehadiran terendah di kelas VII A', 100 + $i)->assertOk();
        }

        $this->assertTrue($this->hasSentText('Terlalu banyak pesan'));
    }

    public function test_unlink_route_removes_link_and_context(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);
        TelegramContext::create(['chat_id' => self::CHAT_ID, 'class_id' => null]);

        $this->actingAs($user)
            ->delete(route('profile.telegram.unlink'))
            ->assertRedirect();

        $this->assertDatabaseMissing('telegram_links', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('telegram_contexts', ['chat_id' => self::CHAT_ID]);
    }

    public function test_export_command_sends_excel_document(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);

        $this->sendMessageUpdate('/ekspor absensi VII A', 20)->assertOk();

        $this->assertTrue($this->hasSentDocument());
    }

    public function test_pagination_callback_shows_next_page(): void
    {
        $user = $this->createMapelWithClass();
        $class = Classes::where('name', 'VII A')->firstOrFail();

        for ($index = 1; $index <= 12; $index++) {
            Students::factory()->create([
                'class_id' => $class->id,
                'name' => sprintf('Siswa %02d', $index),
                'status' => 'ACTIVE',
            ]);
        }

        $this->link($user);
        $this->sendCallbackUpdate('v:a:'.$class->id.':2', 21)->assertOk();

        $this->assertTrue($this->hasSentText('Siswa 12'));
    }

    public function test_below_kkm_query_lists_students(): void
    {
        Settings::factory()->create(['minimum_score' => 75]);

        $user = $this->createMapelWithClass();
        $this->link($user);

        $this->sendMessageUpdate('siapa yang nilainya di bawah KKM di kelas VII A', 22)->assertOk();

        $this->assertTrue($this->hasSentText('di bawah KKM'));
        $this->assertTrue($this->hasSentText('Budi Santoso'));
    }

    public function test_missing_scores_query_lists_students(): void
    {
        $user = $this->createMapelWithClass();
        $class = Classes::where('name', 'VII A')->firstOrFail();
        $budi = Students::where('name', 'Budi Santoso')->firstOrFail();

        $secondTest = DailyTestMeetings::factory()->create([
            'class_id' => $class->id,
            'test_date' => '2026-07-11',
        ]);

        DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $secondTest->id,
            'student_id' => $budi->id,
            'score' => 70,
        ]);

        $this->link($user);
        $this->sendMessageUpdate('siapa yang belum punya nilai UH di kelas VII A', 23)->assertOk();

        $this->assertTrue($this->hasSentText('Nilai Belum Lengkap'));
        $this->assertTrue($this->hasSentText('Siti Aminah'));
    }

    public function test_at_risk_query_lists_students(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);

        $this->sendMessageUpdate('siswa yang perlu perhatian di kelas VII A', 24)->assertOk();

        $this->assertTrue($this->hasSentText('Siswa Perlu Perhatian'));
        $this->assertTrue($this->hasSentText('Budi Santoso'));
        $this->assertTrue($this->hasSentText('kehadiran rendah'));
    }

    public function test_compare_classes_query(): void
    {
        $user = $this->createMapelWithClass();
        $year = AcademicYear::where('user_id', $user->id)->firstOrFail();
        $otherClass = Classes::factory()->create([
            'academic_year_id' => $year->id,
            'name' => 'VII B',
        ]);
        Students::factory()->create([
            'class_id' => $otherClass->id,
            'name' => 'Andi Wijaya',
            'status' => 'ACTIVE',
        ]);

        $this->link($user);
        $this->sendMessageUpdate('bandingkan kelas VII A dan VII B', 25)->assertOk();

        $this->assertTrue($this->hasSentText('Perbandingan Kelas'));
        $this->assertTrue($this->hasSentText('VII B'));
    }

    public function test_search_command_finds_student_across_classes(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);

        $this->sendMessageUpdate('/cari Budi', 26)->assertOk();

        $this->assertTrue($this->hasSentText('Hasil Pencarian'));
        $this->assertTrue($this->hasSentText('Budi Santoso'));
        $this->assertTrue($this->hasSentText('VII A'));
    }

    public function test_ping_and_keyboard_shortcut(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);

        $this->sendMessageUpdate('/ping', 27)->assertOk();
        $this->assertTrue($this->hasSentText('pong'));

        $this->sendMessageUpdate('/bantuan', 28)->assertOk();
        $this->assertTrue($this->hasRemoveKeyboard());

        $this->sendMessageUpdate('Kehadiran', 29)->assertOk();
        $this->assertTrue($this->hasSentText('Rekap Kehadiran'));
    }

    public function test_remove_keyboard_command_cleans_up_linked_chats(): void
    {
        $user = $this->createMapelWithClass();
        $this->link($user);

        $this->artisan('telegram:remove-keyboard')->assertSuccessful();

        $this->assertTrue($this->hasRemoveKeyboard());
    }

    private function createMapelWithClass(string $className = 'VII A', string $secondStudentName = 'Siti Aminah'): User
    {
        $user = User::factory()->create(['role' => 'mapel']);

        $year = AcademicYear::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $class = Classes::factory()->create([
            'academic_year_id' => $year->id,
            'name' => $className,
        ]);

        $budi = Students::factory()->create([
            'class_id' => $class->id,
            'name' => 'Budi Santoso',
            'status' => 'ACTIVE',
        ]);
        $siti = Students::factory()->create([
            'class_id' => $class->id,
            'name' => $secondStudentName,
            'status' => 'ACTIVE',
        ]);

        $meeting1 = AttendanceMeetings::factory()->create([
            'class_id' => $class->id,
            'meeting_date' => '2026-07-01',
        ]);
        $meeting2 = AttendanceMeetings::factory()->create([
            'class_id' => $class->id,
            'meeting_date' => '2026-07-02',
        ]);
        $meeting3 = AttendanceMeetings::factory()->create([
            'class_id' => $class->id,
            'meeting_date' => '2026-07-03',
        ]);

        foreach ([$meeting1, $meeting2, $meeting3] as $meeting) {
            Attendances::factory()->create([
                'attendance_meeting_id' => $meeting->id,
                'student_id' => $siti->id,
                'status' => 'HADIR',
            ]);
        }

        Attendances::factory()->create([
            'attendance_meeting_id' => $meeting1->id,
            'student_id' => $budi->id,
            'status' => 'ALFA',
        ]);
        Attendances::factory()->create([
            'attendance_meeting_id' => $meeting2->id,
            'student_id' => $budi->id,
            'status' => 'ALFA',
        ]);
        Attendances::factory()->create([
            'attendance_meeting_id' => $meeting3->id,
            'student_id' => $budi->id,
            'status' => 'HADIR',
        ]);

        $dailyTest = DailyTestMeetings::factory()->create([
            'class_id' => $class->id,
            'test_date' => '2026-07-10',
        ]);

        DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $dailyTest->id,
            'student_id' => $budi->id,
            'score' => 50,
        ]);
        DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $dailyTest->id,
            'student_id' => $siti->id,
            'score' => 90,
        ]);

        return $user;
    }

    private function link(User $user): TelegramLink
    {
        return TelegramLink::create([
            'user_id' => $user->id,
            'chat_id' => self::CHAT_ID,
            'telegram_username' => 'guru_test',
            'linked_at' => now(),
            'last_active_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function messageUpdate(string $text, int $updateId): array
    {
        return [
            'update_id' => $updateId,
            'message' => [
                'message_id' => $updateId,
                'from' => [
                    'id' => 9001,
                    'is_bot' => false,
                    'first_name' => 'Guru',
                    'username' => 'guru_test',
                ],
                'chat' => [
                    'id' => self::CHAT_ID,
                    'type' => 'private',
                ],
                'date' => time(),
                'text' => $text,
            ],
        ];
    }

    private function sendMessageUpdate(string $text, int $updateId): TestResponse
    {
        return $this->postUpdate($this->messageUpdate($text, $updateId));
    }

    private function sendCallbackUpdate(string $data, int $updateId): TestResponse
    {
        return $this->postUpdate([
            'update_id' => $updateId,
            'callback_query' => [
                'id' => 'callback-'.$updateId,
                'from' => [
                    'id' => 9001,
                    'is_bot' => false,
                    'first_name' => 'Guru',
                    'username' => 'guru_test',
                ],
                'message' => [
                    'message_id' => 1,
                    'chat' => [
                        'id' => self::CHAT_ID,
                        'type' => 'private',
                    ],
                    'date' => time(),
                    'text' => 'Pilih kelas',
                ],
                'chat_instance' => 'test',
                'data' => $data,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $update
     */
    private function postUpdate(array $update): TestResponse
    {
        return $this->withHeader('X-Telegram-Bot-Api-Secret-Token', self::SECRET)
            ->postJson('/api/telegram/webhook', $update);
    }

    private function hasSentText(string $needle): bool
    {
        foreach ($this->sentTexts() as $text) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function sentTexts(): array
    {
        return collect(Http::recorded())
            ->map(fn (array $pair) => $pair[0])
            ->filter(fn ($request): bool => str_contains($request->url(), 'sendMessage'))
            ->map(fn ($request): string => (string) ($request->data()['text'] ?? ''))
            ->values()
            ->all();
    }

    private function sentMessageCount(): int
    {
        return count($this->sentTexts());
    }

    private function hasSentDocument(): bool
    {
        return collect(Http::recorded())
            ->contains(fn (array $pair): bool => str_contains($pair[0]->url(), 'sendDocument'));
    }

    private function hasRemoveKeyboard(): bool
    {
        return collect(Http::recorded())
            ->filter(fn (array $pair): bool => str_contains($pair[0]->url(), 'sendMessage'))
            ->contains(function (array $pair): bool {
                $markup = $pair[0]->data()['reply_markup'] ?? null;

                return is_array($markup) && ($markup['remove_keyboard'] ?? false) === true;
            });
    }
}
