<?php

namespace App\Filament\Teacher\Resources\ScoresResource\Pages;

use App\Filament\Teacher\Resources\ScoresResource;
use App\Models\classes;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListScores extends ListRecords
{
    protected static string $resource = ScoresResource::class;

    protected static ?string $title = 'Nilai Akhir';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Nilai Akhir'),
            Action::make('export')
                ->label('Export To Excel')
                ->modalHeading('Export Data Ke Excel')
                ->form([
                    Select::make('class_id')
                        ->label('Kelas')
                        ->options(classes::all()->pluck('class_name', 'id'))
                ])
                ->action(function($data) {
                    $id = $data['class_id'];

                    return redirect("exportScores/{$id}");
                })
        ];
    }
}
