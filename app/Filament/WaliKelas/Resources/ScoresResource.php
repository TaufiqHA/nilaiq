<?php

namespace App\Filament\WaliKelas\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Scores;
use App\Models\schools;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\WaliKelas\Resources\ScoresResource\Pages;
use App\Filament\WaliKelas\Resources\ScoresResource\RelationManagers;

class ScoresResource extends Resource
{
    protected static ?string $model = Scores::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Nilai Akhir';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('subject_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('teacher_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('academic_year_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('semester_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('score')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('teacher_notes')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('class_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.subject_name')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('subject')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'subject_name')
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('Tidak Ada Nilai Akhir')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScores::route('/'),
            'create' => Pages\CreateScores::route('/create'),
            'edit' => Pages\EditScores::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $school = schools::first();
        $academicYear = $school->academicYear;
        $semester = $school->semester->id;
        return parent::getEloquentQuery()->where('class_id', Auth::user()->class->id)->where('academic_year_id', $academicYear->id)->where('semester_id', $semester);
    }
}
