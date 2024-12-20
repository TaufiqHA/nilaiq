<?php

namespace App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;

use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjectAttendanceSessions extends CreateRecord
{
    protected static string $resource = SubjectAttendanceSessionsResource::class;

    protected static ?string $title = 'Tambah Sesi Absensi';
}
