<?php

namespace App\Filament\Resources\SubjectsResource\Pages;

use App\Filament\Resources\SubjectsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjects extends CreateRecord
{
    protected static string $resource = SubjectsResource::class;

    public function getTitle(): string
    {
        return 'Tambah Mata Pelajaran';
    }
}
