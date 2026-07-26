<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScoreScanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users cannot access the scan route.
     */
    public function test_guest_cannot_access_score_scan(): void
    {
        $response = $this->postJson(route('score-scan'), [
            'image' => UploadedFile::fake()->image('grades.jpg'),
            'students' => json_encode([]),
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test validation rules for scan endpoint.
     */
    public function test_scan_requires_image_and_students(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('score-scan'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image', 'students']);
    }

    /**
     * Test scan returns 500 error when Gemini API Key is missing.
     */
    public function test_scan_fails_without_api_key(): void
    {
        $user = User::factory()->create();
        Config::set('services.gemini.key', null);

        $response = $this->actingAs($user)->postJson(route('score-scan'), [
            'image' => UploadedFile::fake()->image('grades.jpg'),
            'students' => json_encode([
                ['id' => 1, 'name' => 'John Doe'],
            ]),
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Gemini API Key is not configured. Please add GEMINI_API_KEY to your .env file.',
            ]);
    }

    /**
     * Test successful score scanning with mocked Gemini API.
     */
    public function test_scan_scores_successfully(): void
    {
        $user = User::factory()->create();
        Config::set('services.gemini.key', 'fake-key');

        $geminiResponse = [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    [
                                        'student_id' => 1,
                                        'student_name' => 'John Doe',
                                        'score' => 90.5,
                                    ],
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response($geminiResponse, 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('score-scan'), [
            'image' => UploadedFile::fake()->image('grades.jpg'),
            'students' => json_encode([
                ['id' => 1, 'name' => 'John Doe'],
            ]),
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'student_id' => 1,
                        'student_name' => 'John Doe',
                        'score' => 90.5,
                    ],
                ],
            ]);

        Http::assertSent(function ($request): bool {
            return str_contains($request->url(), 'gemini-3.5-flash') &&
                   $request->method() === 'POST';
        });
    }
}
