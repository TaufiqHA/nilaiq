<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Models\Kelas;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\KelasResource;

class EditKelas extends EditRecord
{
    protected static string $resource = KelasResource::class;

    
    protected function resolveRecord($key): Kelas
    {
        return Kelas::withoutGlobalScopes()->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
