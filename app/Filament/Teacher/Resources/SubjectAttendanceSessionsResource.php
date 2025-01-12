<?php

namespace App\Filament\Teacher\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\classes;
use App\Models\schools;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\guruMataPelajaran;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Enums\FiltersLayout;
use App\Models\SubjectAttendanceSessions;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;
use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages\editsubjectAttendanceRecords;

class SubjectAttendanceSessionsResource extends Resource
{
    protected static ?string $model = SubjectAttendanceSessions::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Absensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('subject_id')
                    ->required()
                    ->label('Mata Pelajaran')
                    ->default(guruMataPelajaran::where('teacher_id', auth('teacher')->user()->id)->first()->subject->id),
                Forms\Components\Select::make('class_id')
                    ->required()
                    ->label('Nama Kelas')
                    ->options(classes::all()->pluck('class_name', 'id')),
                Forms\Components\Hidden::make('teacher_id')
                    ->required()
                    ->default(auth('teacher')->user()->id),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('session_name')
                    ->required()
                    ->label('Nama Sesi'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_name')
                    ->searchable()
                    ->label('Sesi'),
                Tables\Columns\TextColumn::make('class.class_name')
                    ->sortable()
                    ->label('Nama Kelas'),
                Tables\Columns\TextColumn::make('subject.subject_name')
                    ->sortable()
                    ->label('Nama Mapel'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('class')
                    ->relationship('class', 'class_name')
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('kelola')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => editsubjectAttendanceRecords::getUrl([$record->id]))
            ])
            ->emptyStateHeading('Tidak Ada Sesi')
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
            'index' => Pages\ListSubjectAttendanceSessions::route('/'),
            'create' => Pages\CreateSubjectAttendanceSessions::route('/create'),
            'edit' => Pages\EditSubjectAttendanceSessions::route('/{record}/edit'),
            'kelola' => Pages\subjectAttendanceRecords::route('/{record}/kelola'),
            'editRecord' => Pages\editsubjectAttendanceRecords::route('/{record}/editRecord'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $school = schools::first();
        $academicYear = $school->academicYear->id;
        $semester = $school->semester->id;
        return parent::getEloquentQuery()->where('teacher_id', auth('teacher')->user()->id)->where('academic_year_id', $academicYear)->where('semester_id', $semester);
    }
}
