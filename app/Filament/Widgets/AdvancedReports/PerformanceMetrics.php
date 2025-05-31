<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PerformanceMetrics extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '15s';
    protected ?string $description = 'Key performance indicators that reflect business process efficiency, including conversion rate, average processing time, cancellation rate, and average order value.';

    protected function getStats(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());
        $compareStartDate = Carbon::parse($this->filters['compareStartDate'] ?? $startDate->copy()->subMonth());
        $compareEndDate = Carbon::parse($this->filters['compareEndDate'] ?? $endDate->copy()->subMonth());
        $compareEnabled = $this->filters['compareEnabled'] ?? false;

        // Current period stats
        $currentStats = $this->getPeriodStats($startDate, $endDate);

        // Comparison period stats
        $compareStats = $compareEnabled ? $this->getPeriodStats($compareStartDate, $compareEndDate) : null;

        return [
            Stat::make('Conversion Rate', number_format($currentStats['conversion_rate'], 2) . '%')
                ->description($this->getComparisonDescription($currentStats['conversion_rate'], $compareStats['conversion_rate'] ?? null))
                ->descriptionIcon($this->getComparisonIcon($currentStats['conversion_rate'], $compareStats['conversion_rate'] ?? null))
                ->color($this->getComparisonColor($currentStats['conversion_rate'], $compareStats['conversion_rate'] ?? null)),

            Stat::make('Average Processing Time', number_format($currentStats['avg_processing_time'], 1) . ' hours')
                ->description($this->getComparisonDescription($currentStats['avg_processing_time'], $compareStats['avg_processing_time'] ?? null))
                ->descriptionIcon($this->getComparisonIcon($currentStats['avg_processing_time'], $compareStats['avg_processing_time'] ?? null))
                ->color($this->getComparisonColor($currentStats['avg_processing_time'], $compareStats['avg_processing_time'] ?? null)),

            Stat::make('Cancellation Rate', number_format($currentStats['cancellation_rate'], 2) . '%')
                ->description($this->getComparisonDescription($currentStats['cancellation_rate'], $compareStats['cancellation_rate'] ?? null))
                ->descriptionIcon($this->getComparisonIcon($currentStats['cancellation_rate'], $compareStats['cancellation_rate'] ?? null))
                ->color($this->getComparisonColor($currentStats['cancellation_rate'], $compareStats['cancellation_rate'] ?? null)),

            Stat::make('Average Order Value', number_format($currentStats['avg_order_value'], 2) . ' SAR')
                ->description($this->getComparisonDescription($currentStats['avg_order_value'], $compareStats['avg_order_value'] ?? null))
                ->descriptionIcon($this->getComparisonIcon($currentStats['avg_order_value'], $compareStats['avg_order_value'] ?? null))
                ->color($this->getComparisonColor($currentStats['avg_order_value'], $compareStats['avg_order_value'] ?? null)),
        ];
    }

    protected function getPeriodStats(Carbon $startDate, Carbon $endDate): array
    {
        // Get total orders and visitors
        $orderStats = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_orders'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_processing_time'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->first();

        // Get total visitors (you'll need to implement this based on your tracking system)
        $totalVisitors = 1000; // This should be replaced with actual visitor count

        return [
            'conversion_rate' => $totalVisitors > 0 ? ($orderStats->total_orders / $totalVisitors) * 100 : 0,
            'avg_processing_time' => $orderStats->avg_processing_time ?? 0,
            'cancellation_rate' => $orderStats->total_orders > 0 ? ($orderStats->cancelled_orders / $orderStats->total_orders) * 100 : 0,
            'avg_order_value' => $orderStats->avg_order_value ?? 0,
        ];
    }

    protected function getComparisonDescription($current, $previous): ?string
    {
        if (!$previous) {
            return null;
        }

        $change = $previous != 0 ? (($current - $previous) / $previous) * 100 : 0;
        return sprintf('%.1f%% from previous period', $change);
    }

    protected function getComparisonIcon($current, $previous): ?string
    {
        if (!$previous) {
            return null;
        }

        return $current > $previous ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
    }

    protected function getComparisonColor($current, $previous): ?string
    {
        if (!$previous) {
            return null;
        }

        return $current > $previous ? 'success' : 'danger';
    }
} 