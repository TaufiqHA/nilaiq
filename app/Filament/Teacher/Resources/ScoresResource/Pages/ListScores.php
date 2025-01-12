<?php

namespace App\Filament\Teacher\Resources\ScoresResource\Pages;

use App\Filament\Teacher\Resources\ScoresResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScores extends ListRecords
{
    protected static string $resource = ScoresResource::class;

    protected static ?string $title = 'Nilai Akhir';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Nilai Akhir'),
        ];
    }
}
