<?php

namespace App\Enums;

enum MaturityActionEnum: string
{
    case TRANSFERRED_TO_SAVINGS = 'TRANSFERRED_TO_SAVINGS';
    case RENEWED = 'RENEWED';
    case PENDING = 'PENDING';

    public function label(): string
    {
        return match($this) {
            self::TRANSFERRED_TO_SAVINGS => 'Transferred to Savings',
            self::RENEWED => 'Renewed',
            self::PENDING => 'Pending',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}