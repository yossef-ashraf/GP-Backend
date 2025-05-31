<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>

    @if ($this->hasHeaderWidgets())
        <x-filament-widgets::widgets
            :columns="$this->getHeaderWidgetsColumns()"
            :widgets="$this->getHeaderWidgets()"
        />
    @endif

    @if ($this->hasFooterWidgets())
        <x-filament-widgets::widgets
            :columns="$this->getFooterWidgetsColumns()"
            :widgets="$this->getFooterWidgets()"
        />
    @endif
</x-filament-panels::page> 