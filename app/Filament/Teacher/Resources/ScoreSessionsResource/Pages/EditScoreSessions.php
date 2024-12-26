<?php

namespace App\Filament\Teacher\Resources\ScoreSessionsResource\Pages;

use App\Filament\Teacher\Resources\ScoreSessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScoreSessions extends EditRecord
{
    protected static string $resource = ScoreSessionsResource::class;
    protected static ?string $title = 'Edit Sesi Nilai';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
