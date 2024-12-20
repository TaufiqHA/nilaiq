<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruMataPelajaranResource\Pages;
use App\Filament\Resources\GuruMataPelajaranResource\RelationManagers;
use App\Models\GuruMataPelajaran;
use App\Models\subjects;
use App\Models\teachers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuruMataPelajaranResource extends Resource
{
    protected static ?string $model = GuruMataPelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Guru Mata Pelajaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('teacher_id')
                    ->required()
                    ->label('Nama Guru')
                    ->options(teachers::all()->pluck('name', 'id')),
                Forms\Components\Select::make('subject_id')
                    ->required()
                    ->label('Mata Pelajaran')
                    ->options(subjects::all()->pluck('subject_name', 'id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Nama Guru'),
                Tables\Columns\TextColumn::make('subject.subject_name')
                    ->label('Mata Pelajaran'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('Tidak Ada Guru Mapel')
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
            'index' => Pages\ListGuruMataPelajarans::route('/'),
            'create' => Pages\CreateGuruMataPelajaran::route('/create'),
            'edit' => Pages\EditGuruMataPelajaran::route('/{record}/edit'),
        ];
    }
}