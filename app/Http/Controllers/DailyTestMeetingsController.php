<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\DailyTestMeetings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyTestMeetingsController extends Controller
{
    /**
     * Display a listing of the daily test meetings.
     */
    public function index(Request $request): JsonResponse|View
    {
        $meetings = DailyTestMeetings::with(['class.students'])->get();
        $classes = Classes::with('students')->get();

        if ($request->wantsJson()) {
            return response()->json($meetings);
        }

        return view('auth.daily-test', compact('meetings', 'classes'));
    }

    /**
     * Store a newly created daily test meeting in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $meeting = DailyTestMeetings::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Daily test meeting created successfully.',
                'data' => $meeting->load('class'),
            ], 201);
        }

        return redirect()->route('daily-test-meetings.index')->with('success', 'Daily test meeting created successfully.');
    }

    /**
     * Display the specified daily test meeting.
     */
    public function show(Request $request, DailyTestMeetings $dailyTestMeeting): JsonResponse|View
    {
        $dailyTestMeeting->load(['class.students']);

        if ($request->wantsJson()) {
            return response()->json($dailyTestMeeting);
        }

        $meetings = DailyTestMeetings::with(['class.students'])->get();
        $classes = Classes::with('students')->get();

        return view('auth.daily-test', compact('meetings', 'classes', 'dailyTestMeeting'));
    }

    /**
     * Update the specified daily test meeting in storage.
     */
    public function update(Request $request, DailyTestMeetings $dailyTestMeeting): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $dailyTestMeeting->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Daily test meeting updated successfully.',
                'data' => $dailyTestMeeting->load('class'),
            ]);
        }

        return redirect()->route('daily-test-meetings.index')->with('success', 'Daily test meeting updated successfully.');
    }

    /**
     * Remove the specified daily test meeting from storage.
     */
    public function destroy(DailyTestMeetings $dailyTestMeeting): JsonResponse|RedirectResponse
    {
        $dailyTestMeeting->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Daily test meeting deleted successfully.',
            ]);
        }

        return redirect()->route('daily-test-meetings.index')->with('success', 'Daily test meeting deleted successfully.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(DailyTestMeetings $dailyTestMeeting): JsonResponse|RedirectResponse
    {
        return $this->destroy($dailyTestMeeting);
    }

    /**
     * Get the validation rules for the daily test meeting request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'test_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
