<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PeakHoursChart extends ChartWidget
{
    protected static ?string $heading = 'Peak Hours';
    protected static ?int $sort = 5;
    protected static ?string $pollingInterval = '15s';
    protected static ?string $maxHeight = '300px';
    protected static ?string $description = 'Analysis of peak hours for orders and sales throughout the day. Helps improve resource allocation and plan marketing campaigns at appropriate times.';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());

        $hourlyData = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Initialize array with all hours
        $hours = range(0, 23);
        $orderCounts = array_fill(0, 24, 0);
        $revenues = array_fill(0, 24, 0);

        // Fill in the actual data
        foreach ($hourlyData as $data) {
            $orderCounts[$data->hour] = $data->order_count;
            $revenues[$data->hour] = $data->total_revenue;
        }

        // Format hours for display
        $labels = array_map(function ($hour) {
            return sprintf('%02d:00', $hour);
        }, $hours);

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'data' => $orderCounts,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                    'fill' => true,
                ],
                [
                    'label' => 'Total Revenue',
                    'data' => $revenues,
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF6384',
                    'yAxisID' => 'y1',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Hour of Day',
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
            'elements' => [
                'line' => [
                    'tension' => 0.4,
                ],
            ],
        ];
    }
} 