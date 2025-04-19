<?php

namespace App\Enums;

enum Product_variationStock_status: string
{
    case IN_STOCK = 'in_stock';
    case OUT_OF_STOCK = 'out_of_stock';
    case BACKORDERED = 'backordered';

    public function getColor(): string
    {
        return match($this) {
            self::IN_STOCK => 'primary',
            self::OUT_OF_STOCK => 'secondary',
            self::BACKORDERED => 'success',
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::IN_STOCK => 'In Stock',
            self::OUT_OF_STOCK => 'Out Of Stock',
            self::BACKORDERED => 'Backordered',
        };
    }
}
