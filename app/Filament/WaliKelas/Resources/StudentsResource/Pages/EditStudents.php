<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Filament\WaliKelas\Resources\StudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudents extends EditRecord
{
    protected static string $resource = StudentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
