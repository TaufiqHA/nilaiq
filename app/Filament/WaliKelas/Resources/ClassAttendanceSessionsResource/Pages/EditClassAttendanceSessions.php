<?php

namespace App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;

use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassAttendanceSessions extends EditRecord
{
    protected static string $resource = ClassAttendanceSessionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
