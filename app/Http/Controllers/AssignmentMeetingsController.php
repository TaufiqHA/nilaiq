<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AssignmentMeetings;
use App\Models\Classes;
use App\Rules\OwnedExists;
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
        $activeYear = AcademicYear::getActive();
        $meetings = AssignmentMeetings::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->whereHas('class', fn ($class) => $class->where('academic_year_id', $activeYear->id)))
            ->with(['class.students', 'scores'])
            ->get();
        $classes = Classes::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->with('students')
            ->get();

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

        if (! empty($validated['client_uuid'])) {
            $existing = AssignmentMeetings::ownedBy(auth()->user())
                ->where('client_uuid', $validated['client_uuid'])
                ->first();

            if ($existing) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Assignment meeting already exists.',
                        'data' => $existing->load('class'),
                    ]);
                }

                return redirect()->route('assignment-meetings.index')->with('success', 'Assignment meeting already exists.');
            }
        }

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

        $activeYear = AcademicYear::getActive();
        $meetings = AssignmentMeetings::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->whereHas('class', fn ($class) => $class->where('academic_year_id', $activeYear->id)))
            ->with(['class.students', 'scores'])
            ->get();
        $classes = Classes::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->with('students')
            ->get();

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
            'client_uuid' => ['nullable', 'uuid'],
            'class_id' => ['required', new OwnedExists(Classes::class)],
            'title' => ['required', 'string', 'max:255'],
            'assignment_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
