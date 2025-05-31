<x-filament::page>
    <x-filament::form wire:submit="generateReport">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Generate Report
        </x-filament::button>
    </x-filament::form>

    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Total Orders</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($data['general_stats']['total_orders']) }}
                    </p>
                </div>
                <div class="rounded-full bg-primary-100 p-3">
                    <x-heroicon-o-shopping-cart class="h-6 w-6 text-primary-600" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Total Revenue</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        ${{ number_format($data['general_stats']['total_revenue'], 2) }}
                    </p>
                </div>
                <div class="rounded-full bg-success-100 p-3">
                    <x-heroicon-o-currency-dollar class="h-6 w-6 text-success-600" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Average Order Value</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        ${{ number_format($data['general_stats']['average_order_value'], 2) }}
                    </p>
                </div>
                <div class="rounded-full bg-warning-100 p-3">
                    <x-heroicon-o-calculator class="h-6 w-6 text-warning-600" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Cancelled Orders</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($data['general_stats']['cancelled_orders']) }}
                    </p>
                </div>
                <div class="rounded-full bg-danger-100 p-3">
                    <x-heroicon-o-x-circle class="h-6 w-6 text-danger-600" />
                </div>
            </div>
        </x-filament::card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Top Products</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['top_products'] as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->total_quantity) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Sales by Area</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['sales_by_area'] as $area)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $area->area_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($area->orders_count) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($area->total_sales, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Sales by Payment Method</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['sales_by_payment_method'] as $method)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($method->payment_method) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($method->orders_count) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($method->total_sales, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Sales by Category</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['sales_by_category'] as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category->category_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($category->orders_count) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($category->total_quantity) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($category->total_sales, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>
</x-filament::page> 