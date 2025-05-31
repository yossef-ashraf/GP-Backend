<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderStatusDistribution extends ChartWidget
{
    protected static ?string $heading = 'Order Status Distribution';
    protected static ?int $sort = 6;
    protected static ?string $pollingInterval = '15s';
    protected static ?string $maxHeight = '300px';
    protected static ?string $description = 'Distribution of orders by status (Pending, Processing, Completed, Cancelled). Helps monitor operational efficiency and identify workflow improvement points.';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());

        $statusDistribution = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('status')
            ->get();

        $statuses = [
            'pending' => ['count' => 0, 'revenue' => 0],
            'processing' => ['count' => 0, 'revenue' => 0],
            'completed' => ['count' => 0, 'revenue' => 0],
            'cancelled' => ['count' => 0, 'revenue' => 0],
        ];

        foreach ($statusDistribution as $status) {
            $statuses[$status->status] = [
                'count' => $status->count,
                'revenue' => $status->total_revenue,
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'data' => array_column($statuses, 'count'),
                    'backgroundColor' => [
                        '#FFCE56', // Pending
                        '#36A2EB', // Processing
                        '#4BC0C0', // Completed
                        '#FF6384', // Cancelled
                    ],
                    'borderColor' => [
                        '#FFCE56',
                        '#36A2EB',
                        '#4BC0C0',
                        '#FF6384',
                    ],
                ],
                [
                    'label' => 'Total Revenue',
                    'data' => array_column($statuses, 'revenue'),
                    'backgroundColor' => [
                        '#FFCE56',
                        '#36A2EB',
                        '#4BC0C0',
                        '#FF6384',
                    ],
                    'borderColor' => [
                        '#FFCE56',
                        '#36A2EB',
                        '#4BC0C0',
                        '#FF6384',
                    ],
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($statuses)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
                        'text' => 'Total Revenue',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'cutout' => '50%',
        ];
    }
} 