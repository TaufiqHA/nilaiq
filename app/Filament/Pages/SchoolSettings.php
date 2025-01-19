<?php

namespace App\Filament\Pages;

use App\Models\schools;
use App\Models\Semester;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\academicYear;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class SchoolSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $title = 'Sekolah';

    protected static ?string $navigationLabel = 'Profil Sekolah';

    protected static string $view = 'filament.pages.school-settings';

    public ?array $data = [];

    public $school;

    public function mount()
    {
        $this->form->fill(schools::first()->attributesToArray());

        $this->school = schools::first();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Profil Sekolah')
                    ->schema([
                        TextInput::make('school_name')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Alamat')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('kelurahan')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('kecamatan')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('kabupaten')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nss')
                            ->label('NSS')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('npsn')
                            ->label('NPSN')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('principal_name')
                            ->label('Kepala Sekolah')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->maxLength(255),
                    ]),
                Fieldset::make('Tanggal Penerimaan Rapor')
                    ->schema([
                        DatePicker::make('tanggalPenerimaan')
                    ])
                    ->columns(1),
                Fieldset::make('Tahun Ajaran')
                    ->schema([
                        Select::make('academic_years_id')
                            ->label('Tahun Ajaran')
                            ->options(academicYear::all()->pluck('name', 'id')),
                        Select::make('semester_id')
                            ->options(Semester::all()->pluck('name', 'id')),
                    ])
            ])
            ->statePath('data');
    }

    public function getFormAction()
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save')
        ];
    }

    public function save()
    {
        $data = $this->form->getState();

        $this->school->update($data);

        Notification::make() 
            ->success()
            ->title('Saved')
            ->send(); 
    }
}
