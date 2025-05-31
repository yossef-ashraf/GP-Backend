<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OrderDataWidget;
use App\Filament\Widgets\OrderReportWidget;
use App\Filament\Widgets\OrdersBarChart;
use App\Services\HijriDateServices;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Get;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;

class OrderPage extends Page
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Orders Report';
    protected static ?string $title = 'Orders Report';
    protected static string $view = 'filament.pages.order';
    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->can('view reports');
    }

    protected function getFooterWidgets(): array
    {
        return [
            OrdersBarChart::class,
            OrderReportWidget::class,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make()->schema([
                    Grid::make(4)->schema([
                        Select::make('dateRange')
                            ->label('Select Date Range')
                            ->options([
                                'today' => 'Today',
                                'yesterday' => 'Yesterday',
                                'this_week' => 'This Week',
                                'last_week' => 'Last Week',
                                'last_7_days' => 'Last 7 Days',
                                'this_month' => 'This Month',
                                'last_month' => 'Last Month',
                                'last_30_days' => 'Last 30 Days',
                                'this_quarter' => 'This Quarter',
                                'last_quarter' => 'Last Quarter',
                                'last_90_days' => 'Last 90 Days',
                                'current_ramadan' => 'Current Ramadan',
                                'last_ramadan' => 'Last Ramadan',
                                'current_eid_fitr' => 'Current Eid al-Fitr',
                                'last_eid_fitr' => 'Last Eid al-Fitr',
                                'current_eid_adha' => 'Current Eid al-Adha',
                                'last_eid_adha' => 'Last Eid al-Adha',
                                'custom' => 'Custom',
                            ])
                            ->live()
                            ->default('this_month')
                            ->afterStateUpdated(function ($state) {
                                if($state){
                                    $this->updateDateRangeSelection($state);
                                }
                            })
                            ->columnSpan(1),
                        DatePicker::make('startDate')
                            ->label('Start Date')
                            ->default($this->getStartDate())
                            ->required()
                            ->native(false)
                            ->live(onBlur: true)
                            ->visible(fn (Get $get) => $get('dateRange') === 'custom')
                            ->afterStateUpdated(function () {
                                $this->data['periodType'] = $this->calculateCustomPeriodType();
                                $this->dispatchFilters();
                            })
                            ->columnSpan(1),

                        DatePicker::make('endDate')
                            ->label('End Date')
                            ->default($this->getEndDate())
                            ->required()
                            ->native(false)
                            ->live(onBlur: true)
                            ->visible(fn (Get $get) => $get('dateRange') === 'custom')
                            ->afterStateUpdated(function () {
                                $this->data['periodType'] = $this->calculateCustomPeriodType();
                                $this->dispatchFilters();
                            })
                            ->columnSpan(1),

                        Select::make('periodType')
                            ->options([
                                'month' => 'Monthly',
                                'week' => 'Weekly',
                                'day' => 'Daily',
                            ])
                            ->default('month')
                            ->live()
                            ->extraAttributes(['w-50'])
                            ->visible(fn (Get $get) => ($get('dateRange') === 'custom' || $get('dateRange') === 'this_quarter') || $get('dateRange') === 'last_quarter' ||  $get('dateRange') === 'last_90_days' )
                            ->afterStateUpdated(fn () => $this->dispatchFilters()),
                    ]),

                    Placeholder::make('')
                        ->content(fn() => ($this->getStartDate()->format('M j, Y'). ' - ' . $this->getEndDate()->format('M j, Y')))
                        ->visible(fn (Get $get) => $get('dateRange'))
                        ->live(onBlur: true)
                        ->columnSpan(2),

                    Toggle::make('compareEnabled')
                        ->afterStateUpdated(function () {
                            $this->dispatchFilters();
                        })
                        ->label(fn (Get $get) =>
                            ($get('dateRange'))
                                ? 'Compare to (' . $this->getStartDateCompare()->format('M j, Y') . ' - ' . $this->getEndDateCompare()->format('M j, Y') . ')'
                                : 'Compare to'
                        )
                        ->live()
                        ->columnSpanFull(),

                    Grid::make(4)->schema([
                        DatePicker::make('compareStartDate')
                            ->label('Compare Start Date')
                            ->default($this->getStartDateCompare())
                            ->placeholder('Select start date')
                            ->native(false)
                            ->visible(fn (Get $get) => $get('dateRange') === 'custom'),
                        DatePicker::make('compareEndDate')
                            ->label('Compare End Date')
                            ->default($this->getEndDateCompare())
                            ->placeholder('Select end date')
                            ->native(false)
                            ->visible(fn (Get $get) => $get('dateRange') === 'custom'),
                    ]),
                ])->columns(3),
            ])->statePath('data');
    }

    public function mount(): void
    {
        $this->initializeDateFilters();
    }

    protected function initializeDateFilters(): void
    {
        $this->data['dateRange'] = $this->data['dateRange'] ?? 'this_month';
        $this->data['periodType'] = $this->determinePeriodType($this->data['dateRange'] ?? 'this_month');
        $this->data['startDate'] = $this->data['startDate'] ?? now()->startOfMonth()->format('Y-m-d');
        $this->data['endDate'] = $this->data['endDate'] ?? now()->format('Y-m-d');
        $this->data['compareEnabled'] = $this->data['compareEnabled'] ?? false;

        $this->dispatchFilters();
    }

    protected function determinePeriodType(string $range): string
    {
        return match ($range) {
            'today', 'yesterday', 'this_week', 'last_week', 'last_7_days' => 'day',
            'this_month', 'last_month', 'last_30_days' => 'day',
            'this_quarter', 'last_quarter' => 'month',
            'last_90_days' => 'day',
            'custom' => $this->calculateCustomPeriodType(),
            default => 'day',
        };
    }

    protected function calculateCustomPeriodType(): string
    {
        if (!isset($this->data['startDate']) || !isset($this->data['endDate'])) {
            return 'day';
        }

        $startDate = Carbon::parse($this->data['startDate']);
        $endDate = Carbon::parse($this->data['endDate']);
        $diffInDays = $startDate->diffInDays($endDate);

        return match (true) {
            $diffInDays <= 31 => 'day',
            $diffInDays <= 90 => 'day',
            $diffInDays <= 365 => 'month',
            default => 'month',
        };
    }

    protected function dispatchFilters(): void
    {
        $this->data['periodType'] =  $this->data['periodType'] ?? $this->determinePeriodType($this->data['dateRange']);

        $this->dispatch('filterDatesUpdated', [
            'startDate' => $this->getStartDate()->format('Y-m-d'),
            'endDate' => $this->getEndDate()->format('Y-m-d'),
            'compareStartDate' => $this->getStartDateCompare()->format('Y-m-d'),
            'compareEndDate' => $this->getEndDateCompare()->format('Y-m-d'),
            'compareEnabled' => $this->data['compareEnabled'] ?? false,
            'periodType' => $this->data['periodType'],
        ]);
    }

    protected function updateDateRangeSelection(string $range): void
    {
        $now = now();
        $hijriService = new HijriDateServices();

        switch ($range) {
            case 'today':
                $this->data['startDate'] = $now->startOfDay()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfDay()->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $previousDay = $now->copy()->subDay();
                $this->data['compareStartDate'] = $previousDay->startOfDay()->format('Y-m-d');
                $this->data['compareEndDate'] = $previousDay->endOfDay()->format('Y-m-d');
                break;

            case 'yesterday':
                $yesterday = $now->copy()->subDay();
                $this->data['startDate'] = $yesterday->startOfDay()->format('Y-m-d');
                $this->data['endDate'] = $yesterday->endOfDay()->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $dayBeforeYesterday = $now->copy()->subDays(2);
                $this->data['compareStartDate'] = $dayBeforeYesterday->startOfDay()->format('Y-m-d');
                $this->data['compareEndDate'] = $dayBeforeYesterday->endOfDay()->format('Y-m-d');
                break;

            case 'this_week':
                $startOfWeek = $now->copy()->startOfWeek(Carbon::SATURDAY);
                $this->data['startDate'] = $startOfWeek->format('Y-m-d');
                $this->data['endDate'] = $now->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $previousWeekStart = $startOfWeek->copy()->subWeek();
                $previousWeekEnd = $previousWeekStart->copy()->endOfWeek(Carbon::FRIDAY);
                $this->data['compareStartDate'] = $previousWeekStart->format('Y-m-d');
                $this->data['compareEndDate'] = $previousWeekEnd->format('Y-m-d');
                break;

            case 'last_week':
                $lastWeekStart = $now->copy()->subWeek()->startOfWeek(Carbon::SATURDAY);
                $lastWeekEnd = $lastWeekStart->copy()->endOfWeek(Carbon::FRIDAY);
                $this->data['startDate'] = $lastWeekStart->format('Y-m-d');
                $this->data['endDate'] = $lastWeekEnd->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $beforeLastWeekStart = $lastWeekStart->copy()->subWeek()->startOfWeek(Carbon::SATURDAY);
                $beforeLastWeekEnd = $beforeLastWeekStart->copy()->endOfWeek(Carbon::FRIDAY);
                $this->data['compareStartDate'] = $beforeLastWeekStart->format('Y-m-d');
                $this->data['compareEndDate'] = $beforeLastWeekEnd->format('Y-m-d');
                break;

            case 'last_7_days':
                $this->data['startDate'] = $now->copy()->subDays(6)->startOfDay()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfDay()->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $this->data['compareStartDate'] = $now->copy()->subDays(13)->startOfDay()->format('Y-m-d');
                $this->data['compareEndDate'] = $now->copy()->subDays(7)->endOfDay()->format('Y-m-d');
                break;

            case 'this_month':
                $this->data['startDate'] = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->data['endDate'] = $now->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $previousMonth = $now->copy()->subMonth();
                $this->data['compareStartDate'] = $previousMonth->startOfMonth()->format('Y-m-d');
                $this->data['compareEndDate'] = $previousMonth->setDay($now->day)->format('Y-m-d');
                break;

            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                $this->data['startDate'] = $lastMonth->startOfMonth()->format('Y-m-d');
                $this->data['endDate'] = $lastMonth->endOfMonth()->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $beforeLastMonth = $lastMonth->copy()->subMonth();
                $this->data['compareStartDate'] = $beforeLastMonth->subMonth()->startOfMonth()->format('Y-m-d');
                $this->data['compareEndDate'] = $beforeLastMonth->endOfMonth()->format('Y-m-d');
                break;

            case 'last_30_days':
                $this->data['startDate'] = $now->copy()->subDays(29)->startOfDay()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfDay()->format('Y-m-d');
                $this->data['periodType'] = 'day';
                $this->data['compareStartDate'] = $now->copy()->subDays(59)->startOfDay()->format('Y-m-d');
                $this->data['compareEndDate'] = $now->copy()->subDays(30)->endOfDay()->format('Y-m-d');
                break;

            case 'this_quarter':
                $this->data['startDate'] = $now->copy()->startOfQuarter()->format('Y-m-d');
                $this->data['endDate'] = $now->format('Y-m-d');
                $this->data['periodType'] = $this->data['periodType'] ?? 'month';
                $previousQuarter = $now->copy()->subQuarter();
                $this->data['compareStartDate'] = $previousQuarter->startOfQuarter()->format('Y-m-d');
                $this->data['compareEndDate'] = $previousQuarter->endOfQuarter()->format('Y-m-d');
                break;

            case 'last_quarter':
                $lastQuarter = $now->copy()->subQuarter();
                $this->data['startDate'] = $lastQuarter->subQuarter()->startOfQuarter()->format('Y-m-d');
                $this->data['endDate'] = $lastQuarter->endOfQuarter()->format('Y-m-d');
                $this->data['periodType'] = $this->data['periodType'] ?? 'month';
                $beforeLastQuarter = $lastQuarter->copy()->subQuarter();
                $this->data['compareStartDate'] = $beforeLastQuarter->startOfQuarter()->format('Y-m-d');
                $this->data['compareEndDate'] = $beforeLastQuarter->endOfQuarter()->format('Y-m-d');
                break;

            case 'last_90_days':
                $this->data['startDate'] = $now->copy()->subDays(89)->startOfDay()->format('Y-m-d');
                $this->data['endDate'] = $now->endOfDay()->format('Y-m-d');
                $this->data['periodType'] = $this->data['periodType'] ?? 'month';
                $this->data['compareStartDate'] = $now->copy()->subDays(179)->startOfDay()->format('Y-m-d');
                $this->data['compareEndDate'] = $now->copy()->subDays(90)->endOfDay()->format('Y-m-d');
                break;

            case 'custom':
                $this->data['startDate'] = $this->data['startDate'] ?? $now->startOfYear()->format('Y-m-d');
                $this->data['endDate'] = $this->data['endDate'] ?? $now->endOfYear()->format('Y-m-d');
                $startDate = Carbon::parse($this->data['startDate']);
                $endDate = Carbon::parse($this->data['endDate']);
                $dateDiff = $startDate->diffInDays($endDate);
                $this->data['compareStartDate'] = $startDate->copy()->subDays($dateDiff + 1)->format('Y-m-d');
                $this->data['compareEndDate'] = $startDate->copy()->subDay()->format('Y-m-d');
                break;

            case 'current_ramadan':
                $ramadan = $hijriService->getRamadanDatesApi();
                $this->data['startDate'] = $ramadan['start'];
                $this->data['endDate'] = $ramadan['end'];
                $this->data['periodType'] = 'day';
                $lastYear = $hijriService->getHijriYear(Carbon::now()->subYear());
                $lastRamadan = $hijriService->getRamadanDatesApi($lastYear);
                $this->data['compareStartDate'] = $lastRamadan['start'];
                $this->data['compareEndDate'] = $lastRamadan['end'];
                break;

            case 'last_ramadan':
                $lastYear = $hijriService->getHijriYear(Carbon::now()->subYear());
                $lastRamadan = $hijriService->getRamadanDatesApi($lastYear);
                $this->data['startDate'] = $lastRamadan['start'];
                $this->data['endDate'] = $lastRamadan['end'];
                $this->data['periodType'] = 'day';
                $beforeLastRamadan = $hijriService->getRamadanDatesApi($lastYear - 1);
                $this->data['compareStartDate'] = $beforeLastRamadan['start'];
                $this->data['compareEndDate'] = $beforeLastRamadan['end'];
                break;

            case 'current_eid_fitr':
                $eidFitr = $hijriService->getEidAlFitrDatesApi();
                $this->data['startDate'] = $eidFitr['start'];
                $this->data['endDate'] = $eidFitr['end'];
                $this->data['periodType'] = 'day';
                $lastYear = $hijriService->getHijriYear(Carbon::now()->subYear());
                $lastEidFitr = $hijriService->getEidAlFitrDatesApi($lastYear);
                $this->data['compareStartDate'] = $lastEidFitr['start'];
                $this->data['compareEndDate'] = $lastEidFitr['end'];
                break;

            case 'last_eid_fitr':
                $currentHijriYear = $hijriService->getHijriYear(Carbon::now()->subYear());
                $lastEidFitr = $hijriService->getEidAlFitrDatesApi($currentHijriYear);
                $this->data['startDate'] = $lastEidFitr['start'];
                $this->data['endDate'] = $lastEidFitr['end'];
                $this->data['periodType'] = 'day';
                $beforeLastEidFitr = $hijriService->getEidAlFitrDatesApi($currentHijriYear - 1);
                $this->data['compareStartDate'] = $beforeLastEidFitr['start'];
                $this->data['compareEndDate'] = $beforeLastEidFitr['end'];
                break;

            case 'current_eid_adha':
                $eidAdha = $hijriService->getEidAlAdhaDatesApi();
                $this->data['startDate'] = $eidAdha['start'];
                $this->data['endDate'] = $eidAdha['end'];
                $this->data['periodType'] = 'day';
                $lastEidAdha = $hijriService->getEidAlAdhaDatesApi($eidAdha['hijri_year'] - 1);
                $this->data['compareStartDate'] = $lastEidAdha['start'];
                $this->data['compareEndDate'] = $lastEidAdha['end'];
                break;

            case 'last_eid_adha':
                $currentHijriYear = $hijriService->getHijriYear(Carbon::now()->subYear());
                $lastEidAdha = $hijriService->getEidAlAdhaDatesApi($currentHijriYear);
                $this->data['startDate'] = $lastEidAdha['start'];
                $this->data['endDate'] = $lastEidAdha['end'];
                $this->data['periodType'] = 'day';
                $beforeLastEidAdha = $hijriService->getEidAlAdhaDatesApi($currentHijriYear - 1);
                $this->data['compareStartDate'] = $beforeLastEidAdha['start'];
                $this->data['compareEndDate'] = $beforeLastEidAdha['end'];
                break;

            default:
                throw new \InvalidArgumentException("Invalid date range: {$range}");
        }

        $this->dispatchFilters();
    }

    public function getEndDate(): Carbon
    {
        return Carbon::parse($this->data['endDate'] ?? now()->endOfMonth());
    }

    public function getStartDate(): Carbon
    {
        return Carbon::parse($this->data['startDate'] ?? now());
    }

    public function getEndDateCompare(): Carbon
    {
        return Carbon::parse($this->data['compareEndDate'] ?? now()->subMonth());
    }

    public function getStartDateCompare(): Carbon
    {
        return Carbon::parse($this->data['compareStartDate'] ?? now()->subMonth()->startOfMonth());
    }
} 