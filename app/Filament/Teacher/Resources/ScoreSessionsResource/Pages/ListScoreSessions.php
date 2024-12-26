<?php

namespace App\Filament\Teacher\Resources\ScoreSessionsResource\Pages;

use App\Filament\Teacher\Resources\ScoreSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScoreSessions extends ListRecords
{
    protected static string $resource = ScoreSessionsResource::class;
    protected static ?string $title = 'Sesi Nilai';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Sesi'),
        ];
    }
}
