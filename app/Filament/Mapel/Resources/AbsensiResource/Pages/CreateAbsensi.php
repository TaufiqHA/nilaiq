<?php

namespace App\Filament\Mapel\Resources\AbsensiResource\Pages;

use App\Models\Mapel;
use App\Models\Absensi;
use App\Models\student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Mapel\Resources\AbsensiResource;

class CreateAbsensi extends CreateRecord
{
    protected static string $resource = AbsensiResource::class;

    protected static string $view = 'filament.resources.absen-resource.pages.form-absensi';

    public function form(Form $form): Form {
        return $form->schema([
            Card::make()->schema([
                Grid::make([
                    'lg' => 3,
                    'md' => 1,
                ])->schema([
                    TextInput::make('name')
                        ->required(),
                    DatePicker::make('tanggal')
                        ->required(),
                    Select::make('mapel_id')
                        ->options(Mapel::all()->pluck('name', 'id'))
                        ->required(),
                    Select::make('kelas_id')
                        ->relationship(name: 'kelas', titleAttribute: 'name')
                        ->required()
                        ->preload()
                        ->live()
                        ->columnSpan([
                            'lg' => 3,
                            'md' => 1                        ]),
                ]),

                // Menggunakan foreach untuk menampilkan semua siswa dalam kelas yang dipilih
                Card::make()->schema(function (Get $get) {
                    $kelas_id = $get('kelas_id');
                    $students = student::where('kelas_id', $kelas_id)->get();

                    return $students->map(function ($student) use ($get) {
                        return Card::make()
                            ->schema([
                                TextInput::make("students[{$student->id}][student_name]")
                                    ->label('Nama Siswa')
                                    ->disabled()
                                    ->placeholder($student->name),

                                Select::make("students[{$student->id}][status]")
                                    ->options([
                                        'hadir' => 'Hadir',
                                        'izin' => 'Izin',
                                        'sakit' => 'Sakit',
                                        'alpha' => 'Alpha'
                                    ])
                                    ->label('Status Kehadiran')
                                    ->required()
                                    ->afterStateUpdated(function ($state) use ($get, $student) {
                                        // Menyimpan data ke database Absensi
                                        Absensi::insert(
                                            [
                                                'name' => $get('name'),
                                                'status' => $state,
                                                'tanggal' => $get('tanggal'),
                                                'student_id' => $student->id,
                                                'mapel_id' => $get('mapel_id'),
                                                'kelas_id' => $get('kelas_id'),
                                                'user_id' => Auth::user()->id,
                                            ]
                                        );
                                    }),
                            ])
                            ->columns(2);
                    })
                    ->toArray();
                })
            ]),
        ]);
    }

    public function save() {
        return redirect()->to('mapel/absensis');
    }
}
