<?php

namespace App\Filament\WaliKelas\Resources;

use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;
use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages\editClassAttendanceRecords;
use App\Models\ClassAttendanceSessions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClassAttendanceSessionsResource extends Resource
{
    protected static ?string $model = ClassAttendanceSessions::class;

    protected static ?string $navigationLabel = 'Absensi';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('class_id')
                    ->required()
                    ->default(auth('waliKelas')->user()->class->id),
                Forms\Components\Hidden::make('wali_kelas_id')
                    ->required()
                    ->default(auth('waliKelas')->user()->id),
                Forms\Components\TextInput::make('session_name')
                    ->required()
                    ->label('Nama Sesi'),
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->label('Tanggal'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_name')
                    ->label('Nama Sesi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('class.class_name')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => editClassAttendanceRecords::getUrl([$record->id])),
            ])
            ->emptyStateHeading('Tidak Ada Absensi')
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
            'index' => Pages\ListClassAttendanceSessions::route('/'),
            'create' => Pages\CreateClassAttendanceSessions::route('/create'),
            'edit' => Pages\EditClassAttendanceSessions::route('/{record}/edit'),
            'kelola' => Pages\classAttendanceRecords::route('/{record}/kelola'),
            'editAbsensi' => Pages\editClassAttendanceRecords::route('/{record}/editAbsensi'),
        ];
    }
}
