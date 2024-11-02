<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Models\Kelas;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\KelasResource;

class ViewKelas extends ViewRecord
{
    protected static string $resource = KelasResource::class;

    protected function resolveRecord($key): Kelas
    {
        return Kelas::withoutGlobalScopes()->findOrFail($key);
    }
}
