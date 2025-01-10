<?php

namespace App\Filament\Resources\ClassesResource\Pages;

use App\Models\classes;
use App\Models\schools;
use App\Models\students;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Imports\StudentsImporter;
use App\Filament\Resources\ClassesResource;
use Filament\Tables\Concerns\InteractsWithTable;

class addStudent extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ClassesResource::class;

    protected static string $view = 'filament.resources.classes-resource.pages.add-student';

    protected static ?string $title = 'Tambah Siswa';

    public $class;

    public function mount($record) {
        $this->class = classes::where('id', $record)->first();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(students::query()->where('class_id', $this->class->id))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('nis')
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(StudentsImporter::class)
            ]);
    }
}
