<?php

namespace App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;

use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;

class subjectAttendanceRecords extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = SubjectAttendanceSessionsResource::class;

    protected static ?string $title = 'Kelola Sesi Absensi';

    protected static string $view = 'filament.teacher.resources.subject-attendance-sessions-resource.pages.subject-attendance-records';

    public function mount($record): void
    {
        //
    }
}
