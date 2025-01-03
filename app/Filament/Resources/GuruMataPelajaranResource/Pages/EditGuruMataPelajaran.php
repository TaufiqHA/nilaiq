<?php

namespace App\Filament\Resources\GuruMataPelajaranResource\Pages;

use App\Filament\Resources\GuruMataPelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuruMataPelajaran extends EditRecord
{
    protected static string $resource = GuruMataPelajaranResource::class;
    protected static ?string $title = 'Edit Guru Mapel';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
