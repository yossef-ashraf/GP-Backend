<?php

namespace App\Enums;

enum UserGender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    public function getColor(): string
    {
        return match($this) {
            self::MALE => 'primary',
            self::FEMALE => 'secondary',
            self::OTHER => 'success',
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::OTHER => 'Other',
        };
    }
}
