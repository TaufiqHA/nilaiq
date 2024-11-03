<?php

namespace App\Filament\Mapel\Resources\NilaiResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Mapel\Resources\NilaiResource;

class ListNilais extends ListRecords
{
    protected static string $resource = NilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Harian' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis_nilai', 'harian')),
            'Tugas' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis_nilai', 'tugas')),
            'UTS' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis_nilai', 'uts')),
            'UAS' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis_nilai', 'uas')),
        ];
    }
}
