<?php

namespace App\Filament\Resources\Pages\Shipping_value;

use App\Filament\Resources\Shipping_valueResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListShipping_value extends ListRecords
{
    protected static string $resource = Shipping_valueResource::class;

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