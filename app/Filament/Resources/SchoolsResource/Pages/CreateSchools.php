<?php

namespace App\Filament\Resources\SchoolsResource\Pages;

use App\Filament\Resources\SchoolsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchools extends CreateRecord
{
    protected static string $resource = SchoolsResource::class;

    public function getTitle(): string
    {
        return 'Tambah Sekolah';
    }
}
