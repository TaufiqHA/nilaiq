<?php

namespace App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Models\subjectAttendanceSessions;
use App\Models\subjectAttendanceRecords;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource;

class editsubjectAttendanceRecords extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = SubjectAttendanceSessionsResource::class;
    protected static ?string $title = 'Absensi';

    protected static string $view = 'filament.teacher.resources.subject-attendance-sessions-resource.pages.editsubject-attendance-records';

    public $session;

    public $absensi = [];

    public function mount($record): void
    {
        $this->session = subjectAttendanceSessions::where('id', $record)->first();

        //Default values
        $this->absensi = subjectAttendanceRecords::where('attendance_session_id', $this->session->id)->get()->map(function ($absensi) {
            return [
                'name' => $absensi->student->name,
                'status' => $absensi->status,
            ];
        })->toArray();       
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('absensi') // Nama yang sesuai dengan data
                    ->label('Daftar Mahasiswa')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Mahasiswa')
                            ->readOnly(),
                        Select::make( 'status')
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
                    ->default($this->absensi) // Set nilai default
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
    foreach ($data['absensi'] as $studentData) {
        // Temukan mahasiswa berdasarkan nama atau ID (ubah sesuai kebutuhan)
        $student = students::where('name', $studentData['name'])->first();

        if ($student) {
            // Cek jika record sudah ada, jika ada lakukan update
            $attendanceRecord = subjectAttendanceRecords::where('attendance_session_id', $this->session->id)
                                                              ->where('student_id', $student->id)
                                                              ->first();

            if ($attendanceRecord) {
                // Jika record ada, update status
                $attendanceRecord->update([
                    'status' => $studentData['status']
                ]);
            } else {
                // Jika record tidak ada, buat data baru
                subjectAttendanceRecords::create([
                    'attendance_session_id' => $this->session->id,
                    'student_id' => $student->id,
                    'status' => $studentData['status']
                ]);
            }
        }
    }

    // Tampilkan notifikasi berhasil dan kembali ke halaman sebelumnya
    return redirect(SubjectAttendanceSessionsResource::getUrl());
}

}
