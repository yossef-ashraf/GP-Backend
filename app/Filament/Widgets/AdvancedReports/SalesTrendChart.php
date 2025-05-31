<?php

namespace App\Filament\Widgets\AdvancedReports;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Trend';
    protected static ?int $sort = 7;
    protected static ?string $pollingInterval = '15s';
    protected static ?string $maxHeight = '300px';
    protected static ?string $description = 'Analysis of sales trends with the ability to compare with previous periods. Helps track sales growth, identify seasonal patterns, and evaluate marketing strategy effectiveness.';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->filters['endDate'] ?? now());
        $compareEnabled = $this->filters['compareEnabled'] ?? false;

        // Current period data
        $currentData = $this->getPeriodData($startDate, $endDate);

        // Comparison period data
        $compareData = $compareEnabled ? $this->getPeriodData(
            $startDate->copy()->subDays($startDate->diffInDays($endDate)),
            $startDate->copy()->subDay()
        ) : null;

        $datasets = [
            [
                'label' => 'Current Period',
                'data' => $currentData['revenue'],
                'backgroundColor' => '#36A2EB',
                'borderColor' => '#36A2EB',
                'fill' => true,
            ],
        ];

        if ($compareData) {
            $datasets[] = [
                'label' => 'Previous Period',
                'data' => $compareData['revenue'],
                'backgroundColor' => '#FF6384',
                'borderColor' => '#FF6384',
                'fill' => true,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $currentData['labels'],
        ];
    }

    protected function getPeriodData(Carbon $startDate, Carbon $endDate): array
    {
        $diffInDays = $startDate->diffInDays($endDate);
        $periodType = $this->determinePeriodType($diffInDays);

        $query = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        switch ($periodType) {
            case 'hourly':
                $query->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00") as period'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy('period')
                ->orderBy('period');
                break;

            case 'daily':
                $query->select(
                    DB::raw('DATE(created_at) as period'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy('period')
                ->orderBy('period');
                break;

            case 'weekly':
                $query->select(
                    DB::raw('YEARWEEK(created_at) as period'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy('period')
                ->orderBy('period');
                break;

            case 'monthly':
                $query->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy('period')
                ->orderBy('period');
                break;
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('period')->map(function ($period) use ($periodType) {
                return $this->formatPeriodLabel($period, $periodType);
            })->toArray(),
            'revenue' => $data->pluck('revenue')->toArray(),
        ];
    }

    protected function determinePeriodType(int $diffInDays): string
    {
        if ($diffInDays <= 1) {
            return 'hourly';
        } elseif ($diffInDays <= 31) {
            return 'daily';
        } elseif ($diffInDays <= 90) {
            return 'weekly';
        } else {
            return 'monthly';
        }
    }

    protected function formatPeriodLabel(string $period, string $periodType): string
    {
        return match ($periodType) {
            'hourly' => Carbon::parse($period)->format('M d, H:i'),
            'daily' => Carbon::parse($period)->format('M d'),
            'weekly' => Carbon::createFromFormat('Y-m-d', Carbon::parse($period)->format('Y-m-d'))->format('M d'),
            'monthly' => Carbon::parse($period)->format('M Y'),
            default => $period,
        };
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
            'elements' => [
                'line' => [
                    'tension' => 0.4,
                ],
            ],
        ];
    }
} 