<?php

namespace App\Filament\Resources\ClassesResource\Pages;

use App\Filament\Resources\ClassesResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class addStudent extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ClassesResource::class;

    protected static string $view = 'filament.resources.classes-resource.pages.add-student';

    public function mount($record) {
        //
    }
}
