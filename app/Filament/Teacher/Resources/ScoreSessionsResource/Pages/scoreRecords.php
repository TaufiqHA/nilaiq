<?php

namespace App\Filament\Teacher\Resources\ScoreSessionsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use Filament\Actions\Action;
use App\Models\scoreSessions;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use League\Csv\Serializer\CastToArray;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Teacher\Resources\ScoreSessionsResource;
use App\Models\scoreRecords as ModelsScoreRecords;

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
            }
        }

        return redirect(ScoreSessionsResource::getUrl());
    }
}
