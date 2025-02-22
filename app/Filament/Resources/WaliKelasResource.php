<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\schools;
use Filament\Forms\Form;
use App\Models\WaliKelas;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WaliKelasResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WaliKelasResource\RelationManagers;

class WaliKelasResource extends Resource
{
    protected static ?string $model = WaliKelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Wali Kelas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('nip'),
                Forms\Components\TextInput::make('email')
                    ->email(),
                Forms\Components\Select::make('class_id')
                    ->required()
                    ->relationship('class', 'class_name', function(Builder $query) {
                        $school = schools::first();
                        $academicYear = $school->academicYear;
                        $query->where('academic_year_id', $academicYear->id)->get();
                    }),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('class.class_name')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->emptyStateHeading('Tidak Ada Wali Kelas')
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListWaliKelas::route('/'),
            'create' => Pages\CreateWaliKelas::route('/create'),
            'edit' => Pages\EditWaliKelas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $school = schools::first();
        $academicYear = $school->academicYear;
        return parent::getEloquentQuery()->whereHas('class', function($query) use ($academicYear) {
            $query->where('academic_year_id', $academicYear->id);
        });
    }
}
