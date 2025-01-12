<?php

namespace App\Filament\WaliKelas\Resources\ScoresResource\Pages;

use App\Filament\WaliKelas\Resources\ScoresResource;
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
                ->label('Tamba Nilai Akhir'),
        ];
    }
}
