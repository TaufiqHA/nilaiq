<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Filament\WaliKelas\Resources\StudentsResource;
use App\Models\Extracurriculars;
use App\Models\students;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ListExtracurriculars extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = StudentsResource::class;

    protected static ?string $title = "List Ekskul";

    protected static string $view = 'filament.wali-kelas.resources.students-resource.pages.list-extracurriculars';

    public $student;

    public function mount($record)
    {
        $this->student = students::where('id', $record)->first();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Extracurriculars::query()->where('student_id', $this->student->id))
            ->columns([
                TextColumn::make('name')
            ])
            ->actions([
                EditAction::make()
                    ->url(fn ($record) => EditExtracurriculars::getUrl([$record->id]))
            ])
            ->emptyStateHeading('Tidak Ada Ekskul')
            ->headerActions([
                Action::make('Tambah Ekskul')
                    ->url(fn ($record) => CreateExtracurriculars::getUrl([$this->student->id]))
            ])
            ->bulkActions([
                BulkAction::make('Delete')
                    ->requiresConfirmation()
                    ->action(fn (Collection $record) => $record->each->delete())
                    ->deselectRecordsAfterCompletion()
            ]);
    }
}
