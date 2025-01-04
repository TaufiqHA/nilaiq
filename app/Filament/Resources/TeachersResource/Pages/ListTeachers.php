<?php

namespace App\Filament\Resources\TeachersResource\Pages;

use App\Filament\Imports\TeachersImporter;
use App\Filament\Resources\TeachersResource;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeachersResource::class;

    public function getTitle(): string
    {
        return 'Guru';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Guru'),
            ImportAction::make()
                ->importer(TeachersImporter::class)
                ->label('Import Guru')
        ];
    }
}
