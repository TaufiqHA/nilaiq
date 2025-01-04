<?php

namespace App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;

use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassAttendanceSessions extends ListRecords
{
    protected static string $resource = ClassAttendanceSessionsResource::class;

    protected static ?string $title = 'Absensi';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Absensi'),
        ];
    }
}
