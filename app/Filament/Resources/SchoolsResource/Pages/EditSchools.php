<?php

namespace App\Filament\Resources\SchoolsResource\Pages;

use App\Filament\Resources\SchoolsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchools extends EditRecord
{
    protected static string $resource = SchoolsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
