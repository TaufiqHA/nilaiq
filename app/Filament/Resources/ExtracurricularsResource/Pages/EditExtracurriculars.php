<?php

namespace App\Filament\Resources\ExtracurricularsResource\Pages;

use App\Filament\Resources\ExtracurricularsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExtracurriculars extends EditRecord
{
    protected static string $resource = ExtracurricularsResource::class;
    protected static ?string $title = 'Edit Ekstrakurikuler';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
