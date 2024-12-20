<?php

namespace App\Filament\Resources\TeachersResource\Pages;

use App\Filament\Resources\TeachersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTeachers extends CreateRecord
{
    protected static string $resource = TeachersResource::class;

    public function getTitle(): string
    {
        return 'Tambah Guru';
    }
}
