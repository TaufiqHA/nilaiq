<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\CalculatesClassRecap;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RekapController extends Controller
{
    use CalculatesClassRecap;

    /**
     * Display a listing of classes for the score recap.
     */
    public function index(): View
    {
        $activeYear = AcademicYear::getActive();
        $classes = $activeYear
            ? Classes::where('academic_year_id', $activeYear->id)->with(['academicYear', 'students'])->get()
            : Classes::with(['academicYear', 'students'])->get();
        $settings = Settings::first();

        return view('auth.rekap', compact('classes', 'settings'));
    }

    /**
     * Get the aggregated score recap data for a specific class with split columns for meetings.
     */
    public function getClassRekapData(Classes $class): JsonResponse
    {
        $recapData = $this->buildClassRecap($class);

        return response()->json([
            'class' => $class->load('academicYear'),
            'meetings' => $recapData['meetings'],
            'rekap' => $recapData['rekap'],
        ]);
    }
}
