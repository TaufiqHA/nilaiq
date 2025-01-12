<?php

namespace App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use Filament\Actions\Action;
use App\Models\guruMataPelajaran;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Models\classAttendanceRecords;
use Filament\Forms\Contracts\HasForms;
use App\Models\classAttendanceSessions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource;

class editClassAttendanceRecords extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = ClassAttendanceSessionsResource::class;

    protected static ?string $title = 'Edit Absensi';

    protected static string $view = 'filament.wali-kelas.resources.class-attendance-sessions-resource.pages.edit-class-attendance-records';

    public $session;

    public $absensi = [];

    public function mount($record)
    {
        $this->session = classAttendanceSessions::where('id', $record)->first();

        $this->absensi = classAttendanceRecords::where('attendance_session_id', $this->session->id)->get()->map(function($absen) {
            return [
                'name' => $absen->student->name,
                'status' => $absen->status 
            ];
        })->toArray();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('absensi')
                    ->label('Daftar Hadir')
                    ->schema([
                        TextInput::make('name')
                            ->readOnly(),
                        Select::make('status')
                            ->label('Status Kehadiran')
                            ->options([
                                'Present' => 'Hadir',
                                'Sick' => 'Sakit',
                                'Absent' => 'Alpha',
                            ])
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->default($this->absensi)
                    ->columns(2)
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }

    public function save()
    {
        $data = $this->form->getState();

        foreach($data['absensi'] as $absensi) {
            $student = students::where('name', $absensi['name'])->first();

            if($student) {
                $attendaceRecord = classAttendanceRecords::where('attendance_session_id', $this->session->id)->where('student_id', $student->id);

                $attendaceRecord->update([
                    'status' => $absensi['status']
                ]);
            }
        }

        return redirect(ClassAttendanceSessionsResource::getUrl());
    }
}
