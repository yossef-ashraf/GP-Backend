<?php

namespace App\Filament\Resources\Pages\Product_category;

use App\Filament\Resources\Product_categoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProduct_category extends ListRecords
{
    protected static string $resource = Product_categoryResource::class;

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