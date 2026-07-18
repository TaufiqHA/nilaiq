<?php

namespace App\Http\Controllers;

use App\Models\AssignmentMeetings;
use App\Models\Classes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentMeetingsController extends Controller
{
    /**
     * Display a listing of the assignment meetings.
     */
    public function index(Request $request): JsonResponse|View
    {
        $meetings = AssignmentMeetings::with(['class.students', 'scores'])->get();
        $classes = Classes::with('students')->get();

        if ($request->wantsJson()) {
            return response()->json($meetings);
        }

        return view('auth.tugas', compact('meetings', 'classes'));
    }

    /**
     * Store a newly created assignment meeting in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $meeting = AssignmentMeetings::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Assignment meeting created successfully.',
                'data' => $meeting->load('class'),
            ], 201);
        }

        return redirect()->route('assignment-meetings.index')->with('success', 'Assignment meeting created successfully.');
    }

    /**
     * Display the specified assignment meeting.
     */
    public function show(Request $request, AssignmentMeetings $assignmentMeeting): JsonResponse|View
    {
        $assignmentMeeting->load(['class.students', 'scores']);

        if ($request->wantsJson()) {
            return response()->json($assignmentMeeting);
        }

        $meetings = AssignmentMeetings::with(['class.students', 'scores'])->get();
        $classes = Classes::with('students')->get();

        return view('auth.tugas', compact('meetings', 'classes', 'assignmentMeeting'));
    }

    /**
     * Update the specified assignment meeting in storage.
     */
    public function update(Request $request, AssignmentMeetings $assignmentMeeting): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $assignmentMeeting->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Assignment meeting updated successfully.',
                'data' => $assignmentMeeting->load('class'),
            ]);
        }

        return redirect()->route('assignment-meetings.index')->with('success', 'Assignment meeting updated successfully.');
    }

    /**
     * Remove the specified assignment meeting from storage.
     */
    public function destroy(AssignmentMeetings $assignmentMeeting): JsonResponse|RedirectResponse
    {
        $assignmentMeeting->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Assignment meeting deleted successfully.',
            ]);
        }

        return redirect()->route('assignment-meetings.index')->with('success', 'Assignment meeting deleted successfully.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(AssignmentMeetings $assignmentMeeting): JsonResponse|RedirectResponse
    {
        return $this->destroy($assignmentMeeting);
    }

    /**
     * Get the validation rules for the assignment meeting request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'assignment_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
