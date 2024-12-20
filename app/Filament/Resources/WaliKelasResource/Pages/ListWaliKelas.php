<?php

namespace App\Filament\Resources\WaliKelasResource\Pages;

use App\Filament\Resources\WaliKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWaliKelas extends ListRecords
{
    protected static string $resource = WaliKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Wali Kelas'),
        ];
    }
}
