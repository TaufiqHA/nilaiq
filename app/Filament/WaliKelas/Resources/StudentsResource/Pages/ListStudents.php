<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Filament\WaliKelas\Resources\StudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentsResource::class;

    protected static ?string $title = 'Siswa';

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
