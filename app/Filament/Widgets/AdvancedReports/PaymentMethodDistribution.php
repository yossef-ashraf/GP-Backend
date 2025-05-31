<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentMethodDistribution extends ChartWidget
{
    protected static ?string $heading = 'Payment Method Distribution';
    protected static ?int $sort = 8;
    protected static ?string $pollingInterval = '15s';
    protected static ?string $maxHeight = '300px';
    protected static ?string $description = 'Distribution of payment methods used in orders (Cash, Card, Online). Helps understand customer preferences and develop appropriate payment strategies.';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());

        $paymentDistribution = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('payment_method')
            ->get();

        $methods = [
            'cash' => ['count' => 0, 'revenue' => 0],
            'card' => ['count' => 0, 'revenue' => 0],
            'online' => ['count' => 0, 'revenue' => 0],
        ];

        foreach ($paymentDistribution as $method) {
            $methods[$method->payment_method] = [
                'count' => $method->count,
                'revenue' => $method->total_revenue,
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'data' => array_column($methods, 'count'),
                    'backgroundColor' => [
                        '#FFCE56', // Cash
                        '#36A2EB', // Card
                        '#4BC0C0', // Online
                    ],
                    'borderColor' => [
                        '#FFCE56',
                        '#36A2EB',
                        '#4BC0C0',
                    ],
                ],
                [
                    'label' => 'Total Revenue',
                    'data' => array_column($methods, 'revenue'),
                    'backgroundColor' => [
                        '#FFCE56',
                        '#36A2EB',
                        '#4BC0C0',
                    ],
                    'borderColor' => [
                        '#FFCE56',
                        '#36A2EB',
                        '#4BC0C0',
                    ],
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($methods)),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
        ];
    }
} 