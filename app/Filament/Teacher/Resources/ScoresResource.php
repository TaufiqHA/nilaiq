<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\ScoresResource\Pages;
use App\Filament\Teacher\Resources\ScoresResource\RelationManagers;
use App\Models\Scores;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('class.class_name')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('Tidak Ada Nilai')
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
}