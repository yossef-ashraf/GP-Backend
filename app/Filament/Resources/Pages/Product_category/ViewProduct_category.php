<?php

namespace App\Filament\Resources\Pages\Product_category;

use App\Filament\Resources\Product_categoryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct_category extends ViewRecord
{
    protected static string $resource = Product_categoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}