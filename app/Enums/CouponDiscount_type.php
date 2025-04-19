<?php

namespace App\Enums;

enum CouponDiscount_type: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function getColor(): string
    {
        return match($this) {
            self::PERCENTAGE => 'primary',
            self::FIXED => 'secondary',
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::PERCENTAGE => 'Percentage',
            self::FIXED => 'Fixed',
        };
    }
}
