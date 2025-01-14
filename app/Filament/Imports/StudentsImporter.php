<?php

namespace App\Filament\Imports;

use App\Models\classes;
use App\Models\schools;
use App\Models\Students;
use App\Models\Attitudes;
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
                    $school = schools::first();
                    $academicYear = $school->academicYear;
                    return classes::where('class_name', $state)
                        ->where('academic_year_id', $academicYear->id)
                        ->first();
                })
                ->rules(['required']),
            ImportColumn::make('nisn'),
            ImportColumn::make('birth_place'),
            ImportColumn::make('birth_date'),
            ImportColumn::make('religion'),
            ImportColumn::make('family_status'),
            ImportColumn::make('child_order')->numeric(),
            ImportColumn::make('address'),
            ImportColumn::make('origin_school'),
            ImportColumn::make('registration_status'),
            ImportColumn::make('accepted_in_class'),
            ImportColumn::make('admission_date'),
            ImportColumn::make('father_name'),
            ImportColumn::make('mother_name'),
            ImportColumn::make('parent_address'),
            ImportColumn::make('father_job'),
            ImportColumn::make('mother_job'),
            ImportColumn::make('parent_phone'),
            ImportColumn::make('guardian_name'),
            ImportColumn::make('guardian_address'),
            ImportColumn::make('guardian_phone'),
            ImportColumn::make('guardian_job'),
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
        $body = 'Your students import has completed and ' . number_format($import->successful_rows) . ' ' . str(string: 'row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
