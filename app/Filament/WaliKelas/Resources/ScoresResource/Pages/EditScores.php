<?php

namespace App\Filament\WaliKelas\Resources\ScoresResource\Pages;

use App\Filament\WaliKelas\Resources\ScoresResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScores extends EditRecord
{
    protected static string $resource = ScoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
