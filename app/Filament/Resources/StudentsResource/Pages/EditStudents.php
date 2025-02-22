<?php

namespace App\Filament\Resources\StudentsResource\Pages;

use App\Filament\Resources\StudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudents extends EditRecord
{
    protected static string $resource = StudentsResource::class;

    protected static ?string $title = 'Edit Siswa';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
