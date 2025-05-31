<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OrderReportWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '15s';

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
            Stat::make('Total Orders', $currentStats['total_orders'])
                ->description($this->getComparisonDescription($currentStats['total_orders'], $compareStats['total_orders'] ?? null))
                ->descriptionIcon($this->getComparisonIcon($currentStats['total_orders'], $compareStats['total_orders'] ?? null))
                ->color($this->getComparisonColor($currentStats['total_orders'], $compareStats['total_orders'] ?? null)),

            Stat::make('Total Revenue', number_format($currentStats['total_revenue'], 2))
                ->description($this->getComparisonDescription($currentStats['total_revenue'], $compareStats['total_revenue'] ?? null))
                ->descriptionIcon($this->getComparisonIcon($currentStats['total_revenue'], $compareStats['total_revenue'] ?? null))
                ->color($this->getComparisonColor($currentStats['total_revenue'], $compareStats['total_revenue'] ?? null)),

            Stat::make('Average Order Value', number_format($currentStats['average_order_value'], 2))
                ->description($this->getComparisonDescription($currentStats['average_order_value'], $compareStats['average_order_value'] ?? null))
                ->descriptionIcon($this->getComparisonIcon($currentStats['average_order_value'], $compareStats['average_order_value'] ?? null))
                ->color($this->getComparisonColor($currentStats['average_order_value'], $compareStats['average_order_value'] ?? null)),
        ];
    }

    protected function getPeriodStats(Carbon $startDate, Carbon $endDate): array
    {
        $stats = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->first();

        return [
            'total_orders' => $stats->total_orders ?? 0,
            'total_revenue' => $stats->total_revenue ?? 0,
            'average_order_value' => $stats->total_orders > 0 ? ($stats->total_revenue / $stats->total_orders) : 0,
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