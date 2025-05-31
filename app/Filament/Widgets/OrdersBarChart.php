<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrdersBarChart extends ChartWidget
{
    protected static ?string $heading = 'Orders Overview';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';

    public function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());
        $periodType = $this->filters['periodType'] ?? 'day';

        $query = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date');

        $data = $query->get();

        $labels = $data->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('M d');
        })->toArray();

        $orders = $data->pluck('total_orders')->toArray();
        $amounts = $data->pluck('total_amount')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'data' => $orders,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
                [
                    'label' => 'Total Amount',
                    'data' => $amounts,
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF6384',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
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
                        'text' => 'Number of Orders',
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Total Amount',
                    ],
                ],
            ],
        ];
    }
} 