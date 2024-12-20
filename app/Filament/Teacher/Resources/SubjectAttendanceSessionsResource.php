<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages;
use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\Pages\subjectAttendanceRecords;
use App\Filament\Teacher\Resources\SubjectAttendanceSessionsResource\RelationManagers;
use App\Models\classes;
use App\Models\SubjectAttendanceSessions;
use App\Models\subjects;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectAttendanceSessionsResource extends Resource
{
    protected static ?string $model = SubjectAttendanceSessions::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Absensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->required()
                    ->label('Mata Pelajaran')
                    ->options(subjects::all()->pluck('subject_name', 'id')),
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
                //
            ])
            ->actions([
                Tables\Actions\Action::make('kelola')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => subjectAttendanceRecords::getUrl([$record->id]))
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
        ];
    }
}
