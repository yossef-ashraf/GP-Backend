<?php

namespace App\Filament\Resources\Pages\Area;

use App\Filament\Resources\AreaResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListArea extends ListRecords
{
    protected static string $resource = AreaResource::class;

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