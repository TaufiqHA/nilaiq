<?php

namespace App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use App\Models\classAttendanceSessions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource;
use App\Models\classAttendanceRecords as ModelsClassAttendanceRecords;

class classAttendanceRecords extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = ClassAttendanceSessionsResource::class;

    protected static ?string $title =  'Absensi';

    protected static string $view = 'filament.wali-kelas.resources.class-attendance-sessions-resource.pages.class-attendance-records';

    public $session;

    public $student = [];

    public function mount($record)
    {
        $this->session = classAttendanceSessions::where('id', $record)->first();

        $this->student = students::where('class_id', $this->session->class_id)->get()->map(function($student) {
           return [
                'name' => $student->name,
                'status' => null,
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
                            ->label('Nama Siswa')
                            ->readOnly(),
                        Select::make('status')
                            ->label('Status Kehadiran')
                            ->options([
                                'Present' => 'Hadir',
                                'Sick' => 'Sakit',
                                'Absent' => 'Alpha',
                            ]),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->default($this->student) // Set nilai default
                    ->columns(2)
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save()
    {
        $data = $this->form->getState();

        foreach ($data['student'] as $studentData) {
            $student = students::where('name', $studentData['name'])->first();

            if ($student) {
                ModelsClassAttendanceRecords::create([
                    'attendance_session_id' => $this->session->id,
                    'student_id' => $student->id,
                    'status' => $studentData['status']
                ]);
            }
        }

        return redirect(ClassAttendanceSessionsResource::getUrl());
    }
}
