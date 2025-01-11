<?php

namespace App\Filament\WaliKelas\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\schools;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use App\Models\ClassAttendanceSessions;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;
use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages\editClassAttendanceRecords;

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
                Hidden::make('academic_year_id')
                    ->default(function() {
                        $school = schools::first();
                        $academicYear = $school->academicYear->id;

                        return $academicYear;
                    }),
                Hidden::make('semester_id')
                    ->default(function() {
                        $school = schools::first();
                        $semester = $school->semester->id;

                        return $semester;
                    }),
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

    public static function getEloquentQuery(): Builder
    {
        $school = schools::first();
        $academicYear = $school->academicYear->id;
        $semester = $school->semester->id;
        return parent::getEloquentQuery()->where('wali_kelas_id', Auth::user()->id)->where('academic_year_id', $academicYear)->where('semester_id', $semester);
    }
}
