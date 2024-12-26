<?php

namespace App\Filament\Teacher\Resources\ScoreSessionsResource\Pages;

use App\Filament\Teacher\Resources\ScoreSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScoreSessions extends CreateRecord
{
    protected static string $resource = ScoreSessionsResource::class;
    protected static ?string $title = 'Tambah Sesi Nilai';

    protected function getRedirectUrl(): string
    {
        $record = $this->record->id;
        return scoreRecords::getUrl([$record]);
    }
}
