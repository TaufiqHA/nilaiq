<?php

namespace App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;

use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubjectAttendanceSessions extends EditRecord
{
    protected static string $resource = SubjectAttendanceSessionsResource::class;

    protected static ?string $title = 'Edit Sesi Absensi';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
