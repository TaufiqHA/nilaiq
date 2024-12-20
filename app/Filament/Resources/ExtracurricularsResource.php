<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtracurricularsResource\Pages;
use App\Filament\Resources\ExtracurricularsResource\RelationManagers;
use App\Models\Extracurriculars;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtracurricularsResource extends Resource
{
    protected static ?string $model = Extracurriculars::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Ekstrakurikuler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('Tidak Ada Ektrakurikuler')
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
            'index' => Pages\ListExtracurriculars::route('/'),
            'create' => Pages\CreateExtracurriculars::route('/create'),
            'edit' => Pages\EditExtracurriculars::route('/{record}/edit'),
        ];
    }
}
