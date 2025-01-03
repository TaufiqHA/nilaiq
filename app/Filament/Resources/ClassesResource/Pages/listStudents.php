<?php

namespace App\Filament\Resources\ClassesResource\Pages;

use App\Models\classes;
use App\Models\students;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Resources\ClassesResource;
use Filament\Tables\Concerns\InteractsWithTable;

class listStudents extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = ClassesResource::class;

    protected static string $view = 'filament.resources.classes-resource.pages.list-students';
    protected static ?string $title = 'List Siswa';

    public $class;

    public function mount($record): void
    {
        $this->class = classes::where('id', $record)->first();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(students::query()->where('class_name', $this->class->class_name))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('nis')
            ]);
    }
}
