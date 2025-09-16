<?php

namespace App\Enums;

enum InterestCalculationStatusEnum: string
{
    case CALCULATED = 'CALCULATED';
    case CREDITED = 'CREDITED';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match($this) {
            self::CALCULATED => 'Calculated',
            self::CREDITED => 'Credited',
            self::FAILED => 'Failed',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}