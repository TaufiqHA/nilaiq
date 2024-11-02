<?php

namespace App\Filament\Mapel\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Absensi;
use App\Models\student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Mapel\Resources\AbsensiResource\Pages;
use App\Filament\Mapel\Resources\AbsensiResource\RelationManagers;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Class Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\Select::make('student_id')
                    ->label('Nama Siswa')
                    ->options(student::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('mapel_id')
                    ->label('Nama Mapel')
                    ->options(Mapel::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('kelas_id')
                    ->label('Nama Kelas')
                    ->options(Kelas::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'alpha' => 'Alpha'
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mapel.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id);
    }
}
