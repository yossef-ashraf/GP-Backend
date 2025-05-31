<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CustomerSegmentationChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Segmentation';
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '15s';
    protected static ?string $maxHeight = '300px';
    protected static ?string $description = 'Analysis of customers and their segmentation (VIP, Regular, Occasional, New) based on order count and total spending. Helps understand customer behavior and develop appropriate marketing strategies.';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());

        // Get customer segments based on order frequency and total spent
        $customerSegments = Order::query()
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'users.id',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->groupBy('users.id')
            ->get()
            ->map(function ($customer) {
                return [
                    'segment' => $this->determineCustomerSegment($customer->order_count, $customer->total_spent),
                    'order_count' => $customer->order_count,
                    'total_spent' => $customer->total_spent,
                ];
            })
            ->groupBy('segment')
            ->map(function ($customers) {
                return [
                    'count' => $customers->count(),
                    'total_spent' => $customers->sum('total_spent'),
                ];
            });

        $segments = [
            'VIP' => $customerSegments['VIP'] ?? ['count' => 0, 'total_spent' => 0],
            'Regular' => $customerSegments['Regular'] ?? ['count' => 0, 'total_spent' => 0],
            'Occasional' => $customerSegments['Occasional'] ?? ['count' => 0, 'total_spent' => 0],
            'New' => $customerSegments['New'] ?? ['count' => 0, 'total_spent' => 0],
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Number of Customers',
                    'data' => array_column($segments, 'count'),
                    'backgroundColor' => [
                        '#FF6384', // VIP
                        '#36A2EB', // Regular
                        '#FFCE56', // Occasional
                        '#4BC0C0', // New
                    ],
                    'borderColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                    ],
                ],
                [
                    'label' => 'Total Revenue',
                    'data' => array_column($segments, 'total_spent'),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                    ],
                    'borderColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                    ],
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => array_keys($segments),
        ];
    }

    protected function determineCustomerSegment(int $orderCount, float $totalSpent): string
    {
        if ($orderCount >= 10 && $totalSpent >= 1000) {
            return 'VIP';
        } elseif ($orderCount >= 5 && $totalSpent >= 500) {
            return 'Regular';
        } elseif ($orderCount >= 2 && $totalSpent >= 200) {
            return 'Occasional';
        } else {
            return 'New';
        }
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
                        'text' => 'Number of Customers',
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