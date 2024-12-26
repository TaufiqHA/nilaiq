<?php

namespace App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource;
use App\Models\subjectAttendanceRecords as ModelsSubjectAttendanceRecords;
use App\Models\subjectAttendanceSessions;
use Filament\Actions\Action;

class subjectAttendanceRecords extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = SubjectAttendanceSessionsResource::class;

    protected static ?string $title = 'Kelola Sesi Absensi';

    protected static string $view = 'filament.teacher.resources.subject-attendance-sessions-resource.pages.subject-attendance-records';

    public $session;
    public $student = [];

    public function mount($record): void
    {
        $this->session = subjectAttendanceSessions::where('id', $record)->first();

        // Default values
        $this->student = students::where('class_id', $this->session->class_id)->get()->map(function ($student) {
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
                Repeater::make('student') // Nama yang sesuai dengan data
                    ->label('Daftar Siswa')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Siswa')
                            ->readOnly(),
                        Select::make(name: 'status')
                            ->label('Status Kehadiran')
                            ->options([
                                'Present' => 'Hadir',
                                'Sick' => 'Sakit',
                                'Absent' => 'Alpha',
                            ])
                            ->required(), // Status harus dipilih
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->default($this->student) // Set nilai default
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
        // Ambil data dari formulir
        $data = $this->form->getState();

        // Loop melalui setiap mahasiswa untuk memperbarui status
        foreach ($data['student'] as $studentData) {
            // Temukan mahasiswa berdasarkan nama atau ID (ubah sesuai kebutuhan)
            $student = students::where('name', $studentData['name'])->first();

            // Perbarui data jika ditemukan
            if ($student) {
                ModelsSubjectAttendanceRecords::create([
                    'attendance_session_id' => $this->session->id,
                    'student_id' => $student->id,
                    'status' => $studentData['status']
                ]);
            }
        }

        // Tampilkan notifikasi berhasil
        return redirect(SubjectAttendanceSessionsResource::getUrl());
    }


}
