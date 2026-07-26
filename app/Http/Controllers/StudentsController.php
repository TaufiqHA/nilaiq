<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Students;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StudentsController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(): JsonResponse
    {
        $activeYear = AcademicYear::getActive();
        $students = $activeYear
            ? Students::whereHas('class', fn ($q) => $q->where('academic_year_id', $activeYear->id))->with('class')->get()
            : Students::with('class')->get();

        return response()->json($students);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $student = Students::create($validated);

        return response()->json([
            'message' => 'Student created successfully.',
            'data' => $student->load('class'),
        ], 201);
    }

    /**
     * Display the specified student.
     */
    public function show(Students $student): JsonResponse
    {
        return response()->json($student->load('class'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Students $student): JsonResponse
    {
        $validated = $request->validate($this->validationRules($student->id));

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully.',
            'data' => $student->load('class'),
        ]);
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Students $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Students $student): JsonResponse
    {
        return $this->destroy($student);
    }

    /**
     * Import multiple students.
     */
    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'students' => ['required', 'array', 'min:1'],
            'students.*.nis' => ['required', 'string', 'max:50', 'distinct', 'unique:students,nis'],
            'students.*.nisn' => ['required', 'string', 'max:50', 'distinct', 'unique:students,nisn'],
            'students.*.name' => ['required', 'string', 'max:255'],
            'students.*.gender' => ['required', 'string', 'in:L,P'],
            'students.*.birth_place' => ['required', 'string', 'max:255'],
            'students.*.birth_date' => ['nullable', 'date'],
            'students.*.address' => ['required', 'string'],
            'students.*.parent_name' => ['required', 'string', 'max:255'],
            'students.*.parent_phone' => ['required', 'string', 'max:50'],
            'students.*.status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ], [
            'students.*.nis.unique' => 'NIS :value sudah terdaftar di sistem.',
            'students.*.nisn.unique' => 'NISN :value sudah terdaftar di sistem.',
            'students.*.nis.distinct' => 'NIS :value duplikat di dalam file import.',
            'students.*.nisn.distinct' => 'NISN :value duplikat di dalam file import.',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['students'] as $studentData) {
                $studentData['class_id'] = $validated['class_id'];
                Students::create($studentData);
            }
        });

        $updatedStudents = Students::with('class')->where('class_id', $validated['class_id'])->get();

        return response()->json([
            'message' => count($validated['students']).' siswa berhasil diimport.',
            'data' => $updatedStudents,
        ]);
    }

    /**
     * Scan student list image and extract details using Gemini.
     */
    public function scanImage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => ['required', 'image', 'max:10240'],
            ]);

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

            $prompt = "You are a teacher's assistant. Analyze the uploaded image which is a photo/scan of a student list (daftar siswa) containing handwritten or printed text.
Your task is to extract information about the students listed in the image.
For each student, extract:
- Name (nama)
- NIS (Nomor Induk Siswa) - if available, otherwise null
- NISN (Nomor Induk Siswa Nasional) - if available, otherwise null
- Gender (L/P) - if available, map to 'L' (Laki-laki) or 'P' (Perempuan) or leave as null if not clear
- Birth Place (tempat lahir) - if available, otherwise null
- Birth Date (tanggal lahir) - if available (in YYYY-MM-DD format if possible), otherwise null
- Parent/Guardian Name (nama orang tua/wali) - if available, otherwise null
- Parent/Guardian Phone (nomor telepon wali) - if available, otherwise null
- Address (alamat) - if available, otherwise null

Return a strict JSON output matching this schema:
[
  {
    \"name\": \"Student Full Name\",
    \"nis\": \"123456\" or null,
    \"nisn\": \"00123456\" or null,
    \"gender\": \"L\" or \"P\" or null,
    \"birth_place\": \"City Name\" or null,
    \"birth_date\": \"YYYY-MM-DD\" or null,
    \"parent_name\": \"Parent Name\" or null,
    \"parent_phone\": \"Phone Number\" or null,
    \"address\": \"Address\" or null
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
                    'message' => 'Gemini API returned an empty response.',
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

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Student Scan Exception: '.$e->getMessage()."\n".$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during scanning: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the validation rules for the students request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(?int $studentId = null): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'nis' => ['required', 'string', 'max:50', 'unique:students,nis'.($studentId ? ','.$studentId : '')],
            'nisn' => ['required', 'string', 'max:50', 'unique:students,nisn'.($studentId ? ','.$studentId : '')],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:L,P'],
            'birth_place' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'address' => ['required', 'string'],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
