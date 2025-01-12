<?php

namespace App\Filament\Teacher\Resources\ScoresResource\Pages;

use App\Filament\Teacher\Resources\ScoresResource;
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
