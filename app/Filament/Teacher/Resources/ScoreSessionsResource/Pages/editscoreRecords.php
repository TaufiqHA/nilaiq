<?php

namespace App\Filament\Teacher\Resources\ScoreSessionsResource\Pages;

use App\Models\schools;
use App\Models\students;
use Filament\Forms\Form;
use App\Models\scoreRecords;
use Filament\Actions\Action;
use App\Models\scoreSessions;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Teacher\Resources\ScoreSessionsResource;

class editscoreRecords extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = ScoreSessionsResource::class;

    protected static ?string $title = 'Edit Nilai Siswa';

    protected static string $view = 'filament.teacher.resources.score-sessions-resource.pages.editscore-records';

    public $nilai = [];

    public $session;

    public function mount($record) {
        // $school = schools::first();
        // $academicYear = $school->academicYear->id;
        // $semester = $school->semester->id;
        // $scoreSum = scoreRecords::where('student_id', 1)->get()->sum('score');
        // $sessionSum = scoreSessions::where('class_id', 1)->where('semester_id', $semester)->count();
        // $score = $scoreSum/$sessionSum;
        // dd($scoreSum);
        
        $this->session = scoreSessions::where('id', $record)->first();

        $this->nilai = scoreRecords::where('score_session_id', $record)->get()->map(function ($nilai) {
            return [
                'name' => $nilai->student->name, 
                'score' => $nilai->score
            ];
        })->toArray();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('nilai')
                    ->label('Daftar Nilai Siswa')
                    ->schema([
                        TextInput::make('name')
                            ->readOnly(),
                        TextInput::make('score')
                            ->numeric()
                            ->required()
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->default($this->nilai)
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

        foreach($data['nilai'] as $dataNilai) {
            $student = students::where('name', $dataNilai['name'])->first();

            if($student) {
                $recordNilai = scoreRecords::where('score_session_id', $this->session->id)->where('student_id', $student->id)->first();

                if($recordNilai) {
                    $recordNilai->update([
                        'score' => $dataNilai['score'],
                    ]);
                }
            }
        }

        return redirect(ScoreSessionsResource::getUrl());
    }
}
