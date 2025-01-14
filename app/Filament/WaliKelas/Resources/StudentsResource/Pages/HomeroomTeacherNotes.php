<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\WaliKelas\Resources\StudentsResource;
use App\Models\HomeroomTeacherNotes as ModelsHomeroomTeacherNotes;

class HomeroomTeacherNotes extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = StudentsResource::class;

    protected static ?string $title = 'Catatan Wali Kelas';

    protected static string $view = 'filament.wali-kelas.resources.students-resource.pages.homeroom-teacher-notes';

    public ?array $data = [];

    public $catatan;
    public $student;

    public function mount($record)
    {
        $this->form->fill(ModelsHomeroomTeacherNotes::firstOrCreate([
            'student_id' => $record
        ])->attributesToArray());

        $this->catatan = ModelsHomeroomTeacherNotes::firstOrCreate([
            'student_id' => $record
        ]);

        $this->student = students::where('id', $this->catatan->student_id)->first();
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            RichEditor::make('note')
                ->label('Catatan')
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
            $this->catatan->update([
                'student_id' => $this->student->id,
                'note' => $data['note'],
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
