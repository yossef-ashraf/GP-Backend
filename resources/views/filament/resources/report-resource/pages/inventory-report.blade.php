<x-filament::page>
    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Low Stock Products</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($data['low_stock_products']->count()) }}
                    </p>
                </div>
                <div class="rounded-full bg-danger-100 p-3">
                    <x-heroicon-o-exclamation-circle class="h-6 w-6 text-danger-600" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Total Stock Value</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        ${{ number_format($data['stock_value'], 2) }}
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
                    <h3 class="text-lg font-medium text-gray-900">Never Ordered Products</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($data['never_ordered_products']->count()) }}
                    </p>
                </div>
                <div class="rounded-full bg-warning-100 p-3">
                    <x-heroicon-o-x-circle class="h-6 w-6 text-warning-600" />
                </div>
            </div>
        </x-filament::card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Low Stock Products</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['low_stock_products'] as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->stock_qty) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->stock_qty * $product->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Most Ordered Products</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ordered</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['most_ordered_products'] as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->total_ordered) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->stock_qty) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>

    <div class="mt-8">
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Never Ordered Products</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['never_ordered_products'] as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->stock_qty) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->stock_qty * $product->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>
</x-filament::page> 