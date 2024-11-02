<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use League\Csv\Reader;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Scopes\KelasScope;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use App\Models\Scopes\StudentScope;
use Filament\Tables\Filters\Filter;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Class Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas.name')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('Semua Tahun Ajaran')
                    ->toggle()
                    ->baseQuery(function(Builder $query){
                        return $query->withoutGlobalScope(StudentScope::class);
                    }),
            ])
            ->actions([
                ViewAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
