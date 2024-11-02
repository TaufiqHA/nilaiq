<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Models\Kelas;
use Filament\Actions;
use App\Models\student;
use Filament\Forms\Form;
use App\Imports\ImportStudent;
use App\Models\Scopes\KelasScope;
use Filament\Forms\Components\Card;
use function Laravel\Prompts\select;

use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.form';

    public $file;

    public function form(Form $form) : Form {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('kelas_id')
                            ->relationship(name: 'kelas', titleAttribute:'name'),
                    ]),
                Repeater::make('siswa')
                    ->schema([
                        TextInput::make('name'),
                    ])
                    ->columns(1)
            ])
            ->columns(1);
    }

    public function save() {
        if($this->file != '') {
            Excel::import(new ImportStudent, $this->file);
        } else {
            $get = $this->form->getState();

            $insert = [];
            foreach($get['siswa'] as $row)
            {
                array_push($insert, [
                    'name' => $row['name'],
                    'kelas_id' => $get['kelas_id']
                ]);
            }

            student::insert($insert);
        }

        return redirect()->to('admin/students');
    }
}
