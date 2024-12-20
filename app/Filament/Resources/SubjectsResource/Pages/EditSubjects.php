<?php

namespace App\Filament\Resources\SubjectsResource\Pages;

use App\Filament\Resources\SubjectsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubjects extends EditRecord
{
    protected static string $resource = SubjectsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
