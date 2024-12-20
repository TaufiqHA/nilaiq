<?php

namespace App\Filament\Resources\GuruMataPelajaranResource\Pages;

use App\Filament\Resources\GuruMataPelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuruMataPelajarans extends ListRecords
{
    protected static string $resource = GuruMataPelajaranResource::class;
    protected static ?string $title = 'Guru Mata Pelajaran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah'),
        ];
    }
}
