<?php

namespace App\Filament\Resources\Pages\Shipping_value;

use App\Filament\Resources\Shipping_valueResource;
use Filament\Resources\Pages\ViewRecord;

class ViewShipping_value extends ViewRecord
{
    protected static string $resource = Shipping_valueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}