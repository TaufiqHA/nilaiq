<?php

namespace App\Filament\Imports;

use App\Models\Teachers;
use Illuminate\Support\Facades\Hash;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class TeachersImporter extends Importer
{
    protected static ?string $model = Teachers::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('nip'),
            ImportColumn::make('email')
                ->rules(['email']),
            ImportColumn::make('phone_number'),
            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['required'])
                ->example(Hash::make('coba')),
        ];
    }

    public function resolveRecord(): ?Teachers
    {
        // return Teachers::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Teachers();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your teachers import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
