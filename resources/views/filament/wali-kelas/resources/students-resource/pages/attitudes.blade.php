<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions :actions="$this->getFormAction()"/>
    </x-filament-panels>
</x-filament-panels::page>
