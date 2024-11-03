<?php

namespace App\Filament\Mapel\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use App\Models\Nilai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Mapel\Resources\NilaiResource\Pages;
use App\Filament\Mapel\Resources\NilaiResource\RelationManagers;
use App\Models\Scopes\KelasScope;
use App\Models\Scopes\NilaiScope;
use Filament\Forms\Components\Checkbox;
use PDO;

class NilaiResource extends Resource
{
    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Grade Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Select::make('mapel_id')
                    ->relationship('mapel', 'name')
                    ->required(),
                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'name')
                    ->required(),
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                Forms\Components\TextInput::make('nilai')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TextInput::make('jenis_nilai')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mapel.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_nilai')
                    ->searchable(),
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
                SelectFilter::make('kelas')
                    ->options(Kelas::all()->pluck('name', 'id'))
                    ->attribute('kelas_id'),
                SelectFilter::make('jenis nilai')
                    ->options([
                        'harian' => 'Harian',
                        'tugas' => 'Tugas',
                        'uts' => 'UTS',
                        'uas' => 'UAS'
                    ])
                    ->attribute('jenis_nilai'),
                Filter::make('tanggal')
                ->form([
                    DatePicker::make('tanggal'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['tanggal'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal', $date),
                        );
                }),

                Filter::make('Semua Tahun Ajaran')
                    ->form([
                        Checkbox::make('all'),
                    ])
                    ->baseQuery(function(Builder $query, array $data): Builder{
                        return $query
                            ->when(
                                $data['all'],
                                fn (Builder $query): Builder => $query->withoutGlobalScope(NilaiScope::class),
                            );
                    })
            ])
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
            'index' => Pages\ListNilais::route('/'),
            'create' => Pages\CreateNilai::route('/create'),
            'edit' => Pages\EditNilai::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id);
    }
}
