<?php

namespace App\Filament\WaliKelas\Resources\ScoresResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\WaliKelas\Resources\ScoresResource;

class ListScores extends ListRecords
{
    protected static string $resource = ScoresResource::class;

    protected static ?string $title = 'Nilai Akhir';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tamba Nilai Akhir'),
        ];
    }
}
