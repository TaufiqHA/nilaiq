<?php

namespace App\Filament\Resources\WaliKelasResource\Pages;

use App\Filament\Resources\WaliKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaliKelas extends EditRecord
{
    protected static string $resource = WaliKelasResource::class;

    protected static ?string $title = 'Edit Wali Kelas Baru';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
