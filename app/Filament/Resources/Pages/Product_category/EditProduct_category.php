<?php

namespace App\Filament\Resources\Pages\Product_category;

use App\Filament\Resources\Product_categoryResource;
use Filament\Resources\Pages\EditRecord;

class EditProduct_category extends EditRecord
{
    protected static string $resource = Product_categoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
            \Filament\Actions\ForceDeleteAction::make(),
            \Filament\Actions\RestoreAction::make(),
        ];
    }
}