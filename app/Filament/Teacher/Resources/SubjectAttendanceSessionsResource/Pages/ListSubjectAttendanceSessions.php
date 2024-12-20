<?php

namespace App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;

use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectAttendanceSessions extends ListRecords
{
    protected static string $resource = SubjectAttendanceSessionsResource::class;
    
    protected static ?string $title = 'Sesi Absensi';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Sesi'),
        ];
    }
}
