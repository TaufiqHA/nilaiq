<?php

namespace App\Filament\Teacher\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\classes;
use App\Models\schools;
use App\Models\subjects;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ScoreSessions;
use Filament\Resources\Resource;
use App\Models\guruMataPelajaran;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Teacher\Resources\ScoreSessionsResource\Pages;
use App\Filament\Teacher\Resources\ScoreSessionsResource\RelationManagers;
use App\Filament\Teacher\Resources\ScoreSessionsResource\Pages\scoreRecords;
use App\Filament\Teacher\Resources\ScoreSessionsResource\Pages\editscoreRecords;

class ScoreSessionsResource extends Resource
{
    protected static ?string $model = ScoreSessions::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Nilai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('subject_id')
                    ->required()
                    ->default(guruMataPelajaran::where('teacher_id', auth('teacher')->user()->id)->first()->subject->id)
                    ->label('Mata Pelajaran'),
                Forms\Components\Select::make('class_id')
                    ->required()
                    ->options(classes::all()->pluck('class_name', 'id'))
                    ->label('Kelas'),
                Forms\Components\Hidden::make('teacher_id')
                    ->required()
                    ->default(auth('teacher')->user()->id),
                Forms\Components\Select::make('score_type')
                    ->required()
                    ->options([
                        'Daily' => 'Harian',
                        'Midterm' => 'UTS',
                        'Final' => 'UAS'
                    ])
                    ->label('Jenis Nilai'),
                Forms\Components\TextInput::make('session_name')
                    ->required()
                    ->label('Nama Sesi'),
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->label('Tanggal'),
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
                Tables\Columns\TextColumn::make('subject.subject_name')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('class.class_name')
                    ->label('Kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('score_type')
                    ->label('Jenis Nilai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('session_name')
                    ->label('Nama Sesi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('class')
                    ->relationship('class', 'class_name')
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('Kelola')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => editscoreRecords::getUrl([$record->id])),
            ])
            ->emptyStateHeading('Tidak Ada Sesi Nilai')
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
            'index' => Pages\ListScoreSessions::route('/'),
            'create' => Pages\CreateScoreSessions::route('/create'),
            'edit' => Pages\EditScoreSessions::route('/{record}/edit'),
            'score' => Pages\scoreRecords::route('/{record}/score'),
            'editScore' => Pages\editscoreRecords::route('/{record}/editScore'),
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
