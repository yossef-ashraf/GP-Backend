<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>

    <div class="mt-6">
        {{ $this->table }}
    </div>

    @if ($this->hasWidgets())
        <x-filament-widgets::widgets
            :columns="$this->getWidgetsColumns()"
            :widgets="$this->getWidgets()"
        />
    @endif
</x-filament-panels::page> 