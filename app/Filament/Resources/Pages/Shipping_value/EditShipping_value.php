<?php

namespace App\Filament\Resources\Pages\Shipping_value;

use App\Filament\Resources\Shipping_valueResource;
use Filament\Resources\Pages\EditRecord;

class EditShipping_value extends EditRecord
{
    protected static string $resource = Shipping_valueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
            \Filament\Actions\ForceDeleteAction::make(),
            \Filament\Actions\RestoreAction::make(),
        ];
    }
}