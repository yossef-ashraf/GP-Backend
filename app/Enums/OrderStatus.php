<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'primary',
            self::PROCESSING => 'secondary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'warning',
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }
}
