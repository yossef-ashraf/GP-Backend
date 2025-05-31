<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use App\Models\Area;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GeographicDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Geographic Distribution';
    protected static ?int $sort = 4;
    protected static ?string $pollingInterval = '15s';
    protected static ?string $maxHeight = '300px';
    protected static ?string $description = 'Distribution of sales and orders by geographic areas. Helps identify the most active areas and strategically direct marketing and expansion efforts.';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());

        $areaDistribution = Order::query()
            ->join('areas', 'orders.area_id', '=', 'areas.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'areas.name',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_revenue')
            )
            ->groupBy('areas.id', 'areas.name')
            ->orderByDesc('order_count')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'data' => $areaDistribution->pluck('order_count')->toArray(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
                [
                    'label' => 'Total Revenue',
                    'data' => $areaDistribution->pluck('total_revenue')->toArray(),
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF6384',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $areaDistribution->pluck('name')->toArray(),
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