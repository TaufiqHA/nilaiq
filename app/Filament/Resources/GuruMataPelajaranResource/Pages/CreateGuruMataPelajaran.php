<?php

namespace App\Filament\Resources\GuruMataPelajaranResource\Pages;

use App\Filament\Resources\GuruMataPelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGuruMataPelajaran extends CreateRecord
{
    protected static string $resource = GuruMataPelajaranResource::class;
    protected static ?string $title = 'Tambah Guru Mapel';
}
