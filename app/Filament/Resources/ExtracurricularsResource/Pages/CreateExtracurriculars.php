<?php

namespace App\Filament\Resources\ExtracurricularsResource\Pages;

use App\Filament\Resources\ExtracurricularsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExtracurriculars extends CreateRecord
{
    protected static string $resource = ExtracurricularsResource::class;
    protected static ?string $title = 'Tambah Ekstrakurikuler';
}
