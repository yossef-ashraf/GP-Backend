<?php

namespace App\Filament\Resources\Pages\Product;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
            // \Filament\Actions\ForceDeleteAction::make(),
            // \Filament\Actions\RestoreAction::make(),
        ];
    }

   
    protected function beforSave()
    {
        dump($this->data);
    }
}