<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Classes;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\academicYear;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClassesResource\Pages;
use App\Filament\Resources\ClassesResource\Pages\listStudents;
use App\Models\schools;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\EditAction;

class ClassesResource extends Resource
{
    protected static ?string $model = Classes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kelas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('class_name')
                    ->label('Nama Kelas')
                    ->required(),
                Forms\Components\Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->required()
                    ->options(academicYear::all()->pluck('name', 'id')),
                Hidden::make('school_id')
                    ->default(schools::first()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('class_name')
                    ->label('Nama Kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academic_year.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => listStudents::getUrl([$record->id])),
                EditAction::make(),
            ])
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
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClasses::route('/create'),
            'edit' => Pages\EditClasses::route('/{record}/edit'),
            'addStudents' => Pages\addStudent::route('/{record}/addStudents'),
            'listStudents' => Pages\listStudents::route('/{record}/listStudents'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $school = schools::first();
        $academicYear = $school->academicYear;
        return parent::getEloquentQuery()->where('academic_year_id', $academicYear->id);
    }
}
