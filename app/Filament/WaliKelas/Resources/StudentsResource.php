<?php

namespace App\Filament\WaliKelas\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Students;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\WaliKelas\Resources\StudentsResource\Pages;
use App\Filament\WaliKelas\Resources\StudentsResource\Pages\Attitudes;
use App\Filament\WaliKelas\Resources\StudentsResource\Pages\HomeroomTeacherNotes;
use App\Filament\WaliKelas\Resources\StudentsResource\Pages\ListAchievement;
use App\Filament\WaliKelas\Resources\StudentsResource\Pages\ListExtracurriculars;
use Filament\Tables\Actions\Action;

class StudentsResource extends Resource
{
    protected static ?string $model = Students::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $title = 'Siswa';

    protected static ?string $navigationLabel = 'Siswa';


    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama')
                ->required(),
            Forms\Components\TextInput::make('nis')
                ->label('NIS')
                ->required(),
            Forms\Components\TextInput::make('nisn')
                ->label('NISN'),
            Forms\Components\TextInput::make('birth_place')
                ->label('Tempat Lahir'),
            Forms\Components\DatePicker::make('birth_date')
                ->label('Tanggal Lahir')
                ->required(),
            Forms\Components\TextInput::make('religion')
                ->label('Agama'),
            Forms\Components\TextInput::make('family_status')
                ->label('Status dalam Keluarga'),
            Forms\Components\TextInput::make('child_order')
                ->label('Anak ke'),
            Forms\Components\Textarea::make('address')
                ->label('Alamat'),
            Forms\Components\TextInput::make('origin_school')
                ->label('Sekolah Asal'),
            Forms\Components\TextInput::make('registration_status')
                ->label('Status Pendaftaran'),
            Forms\Components\TextInput::make('accepted_in_class')
                ->label('Diterima di Kelas'),
            Forms\Components\DatePicker::make('admission_date')
                ->label('Tanggal Penerimaan'),
            Forms\Components\TextInput::make('father_name')
                ->label('Nama Ayah'),
            Forms\Components\TextInput::make('mother_name')
                ->label('Nama Ibu'),
            Forms\Components\Textarea::make('parent_address')
                ->label('Alamat Orang Tua'),
            Forms\Components\TextInput::make('father_job')
                ->label('Pekerjaan Ayah'),
            Forms\Components\TextInput::make('mother_job')
                ->label('Pekerjaan Ibu'),
            Forms\Components\TextInput::make('parent_phone')
                ->label('No. HP Orang Tua'),
            Forms\Components\TextInput::make('guardian_name')
                ->label('Nama Wali'),
            Forms\Components\Textarea::make('guardian_address')
                ->label('Alamat Wali'),
            Forms\Components\TextInput::make('guardian_phone')
                ->label('No. HP Wali'),
            Forms\Components\TextInput::make('guardian_job')
                ->label('Pekerjaan Wali'),
            Forms\Components\Select::make('gender')
                ->label('Jenis Kelamin')
                ->options([
                    'Laki-Laki' => 'Laki-Laki',
                    'Perempuan' => 'Perempuan'
                ])
                ->required(),
            Forms\Components\Select::make('class_id')
                ->label('Kelas')
                ->relationship('class', 'class_name')
                ->required(),
        ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('class.class_name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->emptyStateHeading('Tidak Ada Siswa')
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    CreateAction::make()
                        ->label('prestasi')
                        ->icon('heroicon-o-trophy')
                        ->url(fn($record) => ListAchievement::getUrl([$record->id])),
                    Action::make('Sikap')
                        ->icon('heroicon-o-users')
                        ->url(fn ($record) => Attitudes::getUrl([$record->id])),
                    Action::make('Catatan')
                        ->icon('heroicon-o-pencil')
                        ->url(fn ($record) => HomeroomTeacherNotes::getUrl([$record->id])),
                    Action::make('Ekskul')
                        ->icon('heroicon-o-rectangle-group')
                        ->url(fn ($record) => ListExtracurriculars::getUrl([$record->id])),
                    Action::make('Raport')
                        ->icon('heroicon-o-arrow-down-on-square-stack')
                        ->url(fn ($record) => url("/export/{$record->id}"))
                        ->openUrlInNewTab()
                ]),
        
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
            'create' => Pages\CreateStudents::route('/create'),
            'edit' => Pages\EditStudents::route('/{record}/edit'),
            // prestasi
            'listAchievements' => Pages\ListAchievement::route('/{record}/listAchievements'),
            'createAchievements' => Pages\CreateAchievement::route('/{record}/createAchievements'),
            'editAchievements' => Pages\EditAchievement::route('/{record}/editAchievements'),
            // sikap
            'attitudes' => Pages\Attitudes::route('/{record}/attitudes'),
            'catatan' => Pages\HomeroomTeacherNotes::route('/{record}/catatan'),
            // ekskul
            'listEkskul' => Pages\ListExtracurriculars::route('/{record}/listEkskul'),
            'createEkskul' => Pages\CreateExtracurriculars::route('/{record}/createEkskul'),
            'editEkskul' => Pages\EditExtracurriculars::route('/{record}/editEkskul'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('class_id', Auth::user()->class->id);
    }
}
