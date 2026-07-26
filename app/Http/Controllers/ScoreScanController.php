<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScoreScanController extends Controller
{
    /**
     * Scan image and extract grades mapped to student IDs.
     */
    public function scan(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => ['required', 'image', 'max:10240'],
                'students' => ['required', 'json'],
            ]);

            $students = json_decode($request->input('students'), true);
            $imageFile = $request->file('image');

            if (! $imageFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'No image file uploaded.',
                ], 400);
            }

            $imageData = base64_encode(file_get_contents($imageFile->getRealPath()));
            $mimeType = $imageFile->getMimeType();

            $apiKey = config('services.gemini.key');

            if (! $apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API Key is not configured. Please add GEMINI_API_KEY to your .env file.',
                ], 500);
            }

            /** @var array<int, array{id: int, name: string}> $students */
            $studentListStr = collect($students)->map(function (array $student): string {
                return "ID: {$student['id']}, Name: {$student['name']}";
            })->implode("\n");

            $prompt = "You are a teacher's assistant. Analyze the uploaded image which is a photo of a grading sheet (daftar nilai) containing handwritten scores.
Your task is to map each student's grade in the image to the following list of active students:
---
{$studentListStr}
---

Match the names in the image to the active student list as closely as possible (handling abbreviations, spelling errors, or first name matches). 
Return a strict JSON output matching this schema:
[
  {
    \"student_id\": 123, // The exact ID from the student list
    \"student_name\": \"Original Name from List\",
    \"score\": 85.5 // Numeric score found next to the student's name, or null if not found
  }
]

Do not return any explanation, markdown formatting (like ```json), or extra text. Only return the JSON array.";

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inlineData' => [
                                        'mimeType' => $mimeType,
                                        'data' => $imageData,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: '.$response->body());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to Gemini API. Error: '.$response->status(),
                ], 500);
            }

            /** @var string|null $textResponse */
            $textResponse = $response->json('candidates.0.content.parts.0.text');

            if (! $textResponse) {
                Log::error('Gemini API response part text is empty: '.json_encode($response->json()));

                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API returned an empty response. Make sure the API key is active.',
                ], 500);
            }

            $cleaned = trim($textResponse);
            if (str_starts_with($cleaned, '```')) {
                $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
                $cleaned = preg_replace('/\s*```$/', '', $cleaned);
                $cleaned = trim($cleaned);
            }

            /** @var array|null $extractedData */
            $extractedData = json_decode($cleaned, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini Invalid JSON parsing: '.$textResponse);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse JSON response from Gemini API.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $extractedData,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Score Scan Exception: '.$e->getMessage()."\n".$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during scanning: '.$e->getMessage(),
            ], 500);
        }
    }
}
