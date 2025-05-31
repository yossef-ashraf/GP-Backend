<x-filament::page>
    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Total Customers</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($data['total_customers']) }}
                    </p>
                </div>
                <div class="rounded-full bg-primary-100 p-3">
                    <x-heroicon-o-users class="h-6 w-6 text-primary-600" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Active Customers</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($data['active_customers']) }}
                    </p>
                </div>
                <div class="rounded-full bg-success-100 p-3">
                    <x-heroicon-o-user-group class="h-6 w-6 text-success-600" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Average Order Value</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        ${{ number_format($data['average_order_value'], 2) }}
                    </p>
                </div>
                <div class="rounded-full bg-info-100 p-3">
                    <x-heroicon-o-currency-dollar class="h-6 w-6 text-info-600" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Customer Retention Rate</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ number_format($data['retention_rate'], 1) }}%
                    </p>
                </div>
                <div class="rounded-full bg-warning-100 p-3">
                    <x-heroicon-o-arrow-trending-up class="h-6 w-6 text-warning-600" />
                </div>
            </div>
        </x-filament::card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Top Customers by Revenue</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['top_customers'] as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($customer->total_orders) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($customer->total_spent, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer->last_order_date->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Customer Segments</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Segment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Order Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['customer_segments'] as $segment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $segment->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($segment->customer_count) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($segment->percentage, 1) }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($segment->average_order_value, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>

    <div class="mt-8">
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900">Customer Activity Timeline</h3>
            <div class="mt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Customers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returning Customers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Churned Customers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Growth</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['customer_timeline'] as $period)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $period->period }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($period->new_customers) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($period->returning_customers) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($period->churned_customers) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($period->net_growth) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>
</x-filament::page> 