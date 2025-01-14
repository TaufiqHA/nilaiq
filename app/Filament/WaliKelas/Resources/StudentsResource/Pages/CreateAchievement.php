<?php

namespace App\Filament\WaliKelas\Resources\StudentsResource\Pages;

use App\Models\students;
use Filament\Forms\Form;
use App\Models\Achievement;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\WaliKelas\Resources\StudentsResource;

class CreateAchievement extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = StudentsResource::class;

    protected static ?string $title = 'Tambah Prestasi';

    protected static string $view = 'filament.wali-kelas.resources.students-resource.pages.create-achievement';

    public ?array $data = [];

    public $student;

    public function mount($record)
    {
        $this->student = students::where('id', $record)->first();
        $this->data = [
            'name' => null,
            'note' => null,
        ];
        $this->fill($this->data); // Fills the form with the initial data
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Prestasi')
                    ->required(),
                RichEditor::make('note')
                    ->toolbarButtons([
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->placeholder('catatan')
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
 
            Achievement::create([
                'student_id' => $this->student->id,
                'name' => $data['name'],
                'note' => $data['note'],
            ]);
        } catch (Halt $exception) {
            return;
        }

        Notification::make() 
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send(); 

        return redirect(ListAchievement::getUrl([$this->student->id]));
    }
}
