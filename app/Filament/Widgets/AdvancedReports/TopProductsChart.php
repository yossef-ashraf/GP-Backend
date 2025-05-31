<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Top Selling Products';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());

        $topProducts = Order::query()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.slug',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.slug')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $topProducts->pluck('total_quantity')->toArray(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
                [
                    'label' => 'Revenue',
                    'data' => $topProducts->pluck('total_revenue')->toArray(),
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF6384',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $topProducts->pluck('slug')->map(function ($slug) {
                return ucwords(str_replace('-', ' ', $slug));
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Units Sold',
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }
} 