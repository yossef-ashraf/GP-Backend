<x-filament::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <x-filament::card>
            <div class="flex flex-col items-center justify-center p-6 text-center">
                <div class="rounded-full bg-primary-100 p-3 mb-4">
                    <x-heroicon-o-chart-bar class="h-8 w-8 text-primary-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900">Sales Report</h3>
                <p class="mt-2 text-sm text-gray-500">
                    View detailed sales statistics, revenue analysis, and product performance metrics.
                </p>
                <x-filament::button
                    class="mt-4"
                    wire:click="redirect({{ ReportResource::getUrl('sales') }})"
                >
                    View Report
                </x-filament::button>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex flex-col items-center justify-center p-6 text-center">
                <div class="rounded-full bg-success-100 p-3 mb-4">
                    <x-heroicon-o-cube class="h-8 w-8 text-success-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900">Inventory Report</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Monitor stock levels, track product movement, and analyze inventory value.
                </p>
                <x-filament::button
                    class="mt-4"
                    wire:click="redirect({{ ReportResource::getUrl('inventory') }})"
                >
                    View Report
                </x-filament::button>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex flex-col items-center justify-center p-6 text-center">
                <div class="rounded-full bg-warning-100 p-3 mb-4">
                    <x-heroicon-o-users class="h-8 w-8 text-warning-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900">Customer Report</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Analyze customer behavior, track retention rates, and segment customer data.
                </p>
                <x-filament::button
                    class="mt-4"
                    wire:click="redirect({{ ReportResource::getUrl('customers') }})"
                >
                    View Report
                </x-filament::button>
            </div>
        </x-filament::card>
    </div>
</x-filament::page> 