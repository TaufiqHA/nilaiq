<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\Attitudes as ModelsAttitudes;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\WaliKelas\Resources\StudentsResource;

class Attitudes extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = StudentsResource::class;

    protected static string $view = 'filament.wali-kelas.resources.students-resource.pages.attitudes';

    public ?array $data = [];

    public $sikap;
    public $student;

    public function mount($record)
    {
        $this->form->fill(ModelsAttitudes::firstOrCreate([
            'student_id' => $record
        ])->attributesToArray());

        $this->sikap = ModelsAttitudes::firstOrCreate([
            'student_id' => $record
        ]);

        $this->student = students::where('id', $this->sikap->student_id)->first();
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Textarea::make('faith_and_piety')
                ->label('Beriman, bertaqwa kepada Tuhan Yang Maha Esa dan berakhlak mulia')
                ->placeholder('Deskripsikan beriman, bertaqwa kepada Tuhan Yang Maha Esa dan berakhlak mulia')
                ->nullable(),
            Textarea::make('independent')
                ->label('Mandiri')
                ->placeholder('Deskripsikan kemandirian')
                ->nullable(),
            Textarea::make('teamwork')
                ->label('Bergotong Royong')
                ->placeholder('Deskripsikan gotong royong')
                ->nullable(),
            Textarea::make('creative')
                ->label('Kreatif')
                ->placeholder('Deskripsikan kreativitas')
                ->nullable(),
            Textarea::make('critical_thinking')
                ->label('Bernalar Kritis')
                ->placeholder('Deskripsikan bernalar kritis')
                ->nullable(),
            Textarea::make('global_diversity')
                ->label('Berkebinekaan Global')
                ->placeholder('Deskripsikan berkebinekaan global')
                ->nullable(),

        ])
        ->statePath('data');

    }

    public function getFormAction()
    {
        return [
            Action::make('save')
            ->label('Save')
            ->submit('save')
        ];
    }

    public function save()
    {
        try {
            $data = $this->form->getState();
            $this->sikap->update([
                'student_id' => $this->student->id,
                'faith_and_piety' => $data['faith_and_piety'],
                'independent' => $data['independent'],
                'teamwork' => $data['teamwork'],
                'creative' => $data['creative'],
                'critical_thinking' => $data['critical_thinking'],
                'global_diversity' => $data['global_diversity'],
            ]);
        } catch (Halt $exception) {
            return;
        }

        Notification::make() 
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send(); 
    }
}
