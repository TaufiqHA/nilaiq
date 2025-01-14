<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Models\students;
use Filament\Tables\Table;
use App\Models\Achievement;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\WaliKelas\Resources\StudentsResource;
use App\Filament\WaliKelas\Resources\StudentsResource\Pages\CreateAchievement;

class ListAchievement extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = StudentsResource::class;

    protected static ?string $title = 'Prestasi Siswa';

    protected static string $view = 'filament.wali-kelas.resources.students-resource.pages.list-achievement';

    public $student;

    public function mount($record)
    {
        $this->student = students::where('id', $record)->first();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Achievement::query()->where('student_id', $this->student->id))
            ->columns([
                TextColumn::make('name')
            ])
            ->actions([
                EditAction::make()
                    ->url(fn($record) => EditAchievement::getUrl([$record->id]))
            ])
            ->emptyStateHeading('Tidak Ada Prestasi')
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Prestasi')
                    ->url(CreateAchievement::getUrl([$this->student->id])),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(Collection $record) => $record->each->delete())
                    ->deselectRecordsAfterCompletion()
            ]);
    }
}
