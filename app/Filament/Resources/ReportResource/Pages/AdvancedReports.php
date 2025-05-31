<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Filament\Widgets\AdvancedReports\TopProductsChart;
use App\Filament\Widgets\AdvancedReports\CustomerSegmentationChart;
use App\Filament\Widgets\AdvancedReports\GeographicDistributionChart;
use App\Filament\Widgets\AdvancedReports\PeakHoursChart;
use App\Filament\Widgets\AdvancedReports\OrderStatusDistribution;
use App\Filament\Widgets\AdvancedReports\SalesTrendChart;
use App\Filament\Widgets\AdvancedReports\PaymentMethodDistribution;
use App\Filament\Widgets\AdvancedReports\PerformanceMetrics;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;

class AdvancedReports extends Page
{
    protected static string $resource = ReportResource::class;
    protected static string $view = 'filament.resources.report-resource.pages.advanced-reports';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $title = 'Advanced Reports';
    protected static ?int $navigationSort = 2;

    public array $data = [];

    public function mount(): void
    {
        $this->initializeFilters();
    }

    protected function initializeFilters(): void
    {
        $this->data = [
            'dateRange' => 'this_month',
            'startDate' => now()->startOfMonth()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
            'orderStatus' => 'all',
            'paymentMethod' => 'all',
            'area' => 'all',
            'category' => 'all',
            'compareEnabled' => false,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filters')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('dateRange')
                                    ->label('Date Range')
                                    ->options([
                                        'today' => 'Today',
                                        'yesterday' => 'Yesterday',
                                        'this_week' => 'This Week',
                                        'last_week' => 'Last Week',
                                        'this_month' => 'This Month',
                                        'last_month' => 'Last Month',
                                        'this_quarter' => 'This Quarter',
                                        'last_quarter' => 'Last Quarter',
                                        'this_year' => 'This Year',
                                        'last_year' => 'Last Year',
                                        'custom' => 'Custom Range',
                                    ])
                                    ->default('this_month')
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->updateDateRange()),

                                DatePicker::make('startDate')
                                    ->label('Start Date')
                                    ->visible(fn (Get $get) => $get('dateRange') === 'custom')
                                    ->live(),

                                DatePicker::make('endDate')
                                    ->label('End Date')
                                    ->visible(fn (Get $get) => $get('dateRange') === 'custom')
                                    ->live(),

                                Select::make('orderStatus')
                                    ->label('Order Status')
                                    ->options([
                                        'all' => 'All Statuses',
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('all')
                                    ->live(),

                                Select::make('paymentMethod')
                                    ->label('Payment Method')
                                    ->options([
                                        'all' => 'All Methods',
                                        'cash' => 'Cash',
                                        'card' => 'Card',
                                        'online' => 'Online',
                                    ])
                                    ->default('all')
                                    ->live(),

                                Select::make('area')
                                    ->label('Area')
                                    ->options([
                                        'all' => 'All Areas',
                                        // Add your areas here
                                    ])
                                    ->default('all')
                                    ->live(),

                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'all' => 'All Categories',
                                        // Add your categories here
                                    ])
                                    ->default('all')
                                    ->live(),

                                Toggle::make('compareEnabled')
                                    ->label('Enable Comparison')
                                    ->default(false)
                                    ->live(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PerformanceMetrics::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TopProductsChart::class,
            CustomerSegmentationChart::class,
            GeographicDistributionChart::class,
            PeakHoursChart::class,
            OrderStatusDistribution::class,
            SalesTrendChart::class,
            PaymentMethodDistribution::class,
        ];
    }

    protected function updateDateRange(): void
    {
        $range = $this->data['dateRange'];
        $now = now();

        switch ($range) {
            case 'today':
                $this->data['startDate'] = $now->format('Y-m-d');
                $this->data['endDate'] = $now->format('Y-m-d');
                break;
            case 'yesterday':
                $this->data['startDate'] = $now->subDay()->format('Y-m-d');
                $this->data['endDate'] = $now->format('Y-m-d');
                break;
            case 'this_week':
                $this->data['startDate'] = $now->startOfWeek()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $this->data['startDate'] = $now->subWeek()->startOfWeek()->format('Y-m-d');
                $this->data['endDate'] = $now->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->data['startDate'] = $now->startOfMonth()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->data['startDate'] = $now->subMonth()->startOfMonth()->format('Y-m-d');
                $this->data['endDate'] = $now->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $this->data['startDate'] = $now->startOfQuarter()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfQuarter()->format('Y-m-d');
                break;
            case 'last_quarter':
                $this->data['startDate'] = $now->subQuarter()->startOfQuarter()->format('Y-m-d');
                $this->data['endDate'] = $now->subQuarter()->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $this->data['startDate'] = $now->startOfYear()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfYear()->format('Y-m-d');
                break;
            case 'last_year':
                $this->data['startDate'] = $now->subYear()->startOfYear()->format('Y-m-d');
                $this->data['endDate'] = $now->subYear()->endOfYear()->format('Y-m-d');
                break;
        }
    }
} 