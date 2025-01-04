<?php

namespace App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;

use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClassAttendanceSessions extends CreateRecord
{
    protected static string $resource = ClassAttendanceSessionsResource::class;

    protected static ?string $title = 'Tambah Absensi Baru';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('kelola', [$this->record->id]);
    }
}
