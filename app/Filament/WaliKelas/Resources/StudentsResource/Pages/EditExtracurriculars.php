<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\Action;
use App\Models\Extracurriculars;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\WaliKelas\Resources\StudentsResource;

class EditExtracurriculars extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = StudentsResource::class;

    protected static string $view = 'filament.wali-kelas.resources.students-resource.pages.edit-extracurriculars';

    protected static ?string $title = 'Edit Ekskul';

    public ?array $data = [];

    public $ekskul;

    public function mount($record)
    {
        $this->ekskul = Extracurriculars::where('id', $record)->first();

        $this->form->fill($this->ekskul->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Ekskul'),
                RichEditor::make('note')
                    ->label('Catatan')
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

        $this->ekskul->update($data);

        Notification::make()
            ->success()
            ->title('Ekskul Berhasil Diupdate')
            ->send();
            
        return redirect(ListExtracurriculars::getUrl([$this->ekskul->student_id]));
    }
}
