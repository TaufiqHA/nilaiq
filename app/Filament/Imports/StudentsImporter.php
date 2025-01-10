<?php

namespace App\Filament\Imports;

use App\Models\classes;
use App\Models\Students;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class StudentsImporter extends Importer
{
    protected static ?string $model = Students::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('nis')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('gender')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('class')
                ->relationship(resolveUsing: function (string $state): ?classes {
                    return classes::latest()
                        ->first();
                })
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?Students
    {
        // return Students::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Students();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your students import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
