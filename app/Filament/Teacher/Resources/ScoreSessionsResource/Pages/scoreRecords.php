<?php

namespace App\Filament\Teacher\Resources\ScoreSessionsResource\Pages;

use App\Models\Scores;
use App\Models\schools;
use App\Models\students;
use Filament\Forms\Form;
use Filament\Actions\Action;
use App\Models\scoreSessions;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Contracts\HasForms;
use League\Csv\Serializer\CastToArray;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\scoreRecords as ModelsScoreRecords;
use App\Filament\Teacher\Resources\ScoreSessionsResource;

class scoreRecords extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = ScoreSessionsResource::class;

    protected static ?string $title = 'Nilai Siswa';

    protected static string $view = 'filament.teacher.resources.score-sessions-resource.pages.score-records';

    public $session;

    public $student = [];

    public function mount($record) {
        $this->session = scoreSessions::where('id', $record)->first();

        $this->student = students::where('class_id', $this->session->class_id)->get()->map(function ($student) {
            return [
                'name' => $student->name,
                'score' => null,
            ];
        })->toArray();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('student')
                    ->label('Daftar Siswa')
                    ->schema([
                        TextInput::make('name')
                            ->readOnly(),
                        TextInput::make('score')
                            ->numeric()
                            ->required()
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->default($this->student)
                    ->columns(2)
                ]);
    }

    public function getFormActions() {
        return [
            Action::make('save')
                ->label('save')
                ->submit('save')
        ];
    }

    public function save() {
        $data = $this->form->getState();

        foreach($data['student'] as $studentData) {
            $student = students::where('name', $studentData['name'])->first();

            if($student) {
                ModelsScoreRecords::create([
                    'score_session_id' => $this->session->id,
                    'student_id' => $student->id,
                    'score' => $studentData['score']
                ]);

                $school = schools::first();
                $academicYear = $school->academicYear->id;
                $semester = $school->semester->id;

                $scoreSum = ModelsScoreRecords::where('student_id', $student->id)->sum('score');
                $sessionSum = scoreSessions::where('class_id', $this->session->class->id)->where('semester_id', $semester)->count();
                $score = round($scoreSum/$sessionSum);

                $data = [
                    'student_id' => $student->id,
                    'subject_id' => $this->session->subject->id,
                    'teacher_id' => $this->session->teacher->id,
                    'class_id' => $this->session->class->id,
                    'academic_year_id' => $academicYear,
                    'semester_id' => $semester,
                ];
                
                $values = [
                    'score' => $score,
                ];
                
                // Lakukan update atau insert
                DB::table('scores')->updateOrInsert($data, $values);
            }
        }

        return redirect(ScoreSessionsResource::getUrl());
    }
}
