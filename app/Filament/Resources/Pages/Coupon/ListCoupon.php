<?php

namespace App\Filament\Resources\Pages\Coupon;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCoupon extends ListRecords
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }

       // public function getTabs(): array
    // {
    //     return [
    //         'all' => Tab::make('All Records'),
    //         'active' => Tab::make('Active')
    //             ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('created_at')),
    //     ];
    // }
}