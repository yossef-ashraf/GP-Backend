<?php

namespace App\Enums;

enum OrderPayment_method: string
{
    case CASH = 'cash';
    case CREDIT_CARD = 'credit_card';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';

    public function getColor(): string
    {
        return match($this) {
            self::CASH => 'primary',
            self::CREDIT_CARD => 'secondary',
            self::PAYPAL => 'success',
            self::BANK_TRANSFER => 'danger',
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::CASH => 'Cash',
            self::CREDIT_CARD => 'Credit Card',
            self::PAYPAL => 'Paypal',
            self::BANK_TRANSFER => 'Bank Transfer',
        };
    }
}
