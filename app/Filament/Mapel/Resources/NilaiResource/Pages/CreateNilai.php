<?php

namespace App\Filament\Mapel\Resources\NilaiResource\Pages;

use PDO;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Mapel\Resources\NilaiResource;
use Filament\Forms\Components\Grid;

class CreateNilai extends CreateRecord
{
    protected static string $resource = NilaiResource::class;

    protected static string $view = 'filament.resources.nilai-resource.pages.form';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'lg' => 3,
                    'md' => 1
                ])->schema([
                    TextInput::make('name')
                        ->required(),
                    // Select::make('mapel_id')
                    //     ->label('Mata Pelajaran')
                    //     ->relationship('mapel', 'name')
                    //     ->required(),
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->relationship('kelas', 'name')
                        ->live()
                        ->required(),
                    DatePicker::make('tanggal')
                        ->required(),
                    Select::make('jenis_nilai')
                        ->options([
                            'harian' => 'Harian',
                            'tugas' => 'Tugas',
                            'uts' => 'UTS',
                            'uas' => 'UAS'
                        ])
                        ->required()
                        ->columnSpan([
                            'lg' => 3,
                            'md' => 1
                        ]),
                    ]),

                Card::make()
                    ->schema(function (Get $get) 
                    {
                        $students = student::where('kelas_id', $get('kelas_id'))->get();

                        return $students->map(function ($student) use ($get) 
                        {
                            return Card::make()
                                ->schema([
                                    TextInput::make("students[{$student->id}][student_name]")
                                        ->label('Nama Siswa')
                                        ->placeholder($student->name)
                                        ->disabled(),
                                    TextInput::make("students[{$student->id}][nilai]")
                                        ->label('Nilai')
                                        ->required()
                                        ->afterStateUpdated(function ($state) use ($get, $student)
                                        {
                                            $mapel = Mapel::whereHas('user', function($query){
                                                $query->where('id', Auth::user()->id);
                                            })->first();
                                            $id = $mapel->id;
                                            Nilai::insert([
                                                'name' => $get('name'),
                                                'mapel_id' => $id,
                                                'kelas_id' => $get('kelas_id'),
                                                'student_id' => $student->id,
                                                'nilai' => $state,
                                                'tanggal' => $get('tanggal'),
                                                'jenis_nilai' => $get('jenis_nilai'),
                                                'user_id' => Auth::user()->id,
                                            ]);
                                        }),
                                ])
                                ->columns(2);
                        })->toArray();
                    })
            ])
            ->columns(3);
    }

    public function save() {
        return redirect()->to('mapel/nilais');
    }
}
