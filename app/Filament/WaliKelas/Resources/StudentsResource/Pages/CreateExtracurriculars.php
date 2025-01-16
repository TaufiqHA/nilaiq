<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Filament\WaliKelas\Resources\StudentsResource;
use App\Models\Extracurriculars;
use App\Models\students;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class CreateExtracurriculars extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = StudentsResource::class;

    protected static ?string $title = 'Tambah Ekskul';

    protected static string $view = 'filament.wali-kelas.resources.students-resource.pages.create-extracurriculars';

    public ?array $data = [];

    public $student;

    public function mount($record)
    {
        $this->student = students::where('id', $record)->first();
        $this->data = [
            'name' => null,
            'note' => null,
        ];
        $this->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Ekskul'),
                RichEditor::make('note')
                    ->label('Catatan')
                    ->placeholder('catatan')
            ])
            ->statePath('data');
    }

    public function getFormActions()
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save')
        ];
    }

    public function save()
    {
        $data = $this->form->getState();

        Extracurriculars::create([
            'student_id' => $this->student->id,
            'name' => $data['name'],
            'note' => $data['note']
        ]);

        Notification::make()
            ->success()
            ->title('Ekskul Berhasil Ditambahkan')
            ->send();
            
        return redirect(ListExtracurriculars::getUrl([$this->student->id]));
    }
}
