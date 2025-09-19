<?php

namespace App\Enums;

enum InterestFrequencyEnum: string
{
    case MONTHLY = 'MONTHLY';
    case END = 'END';

    public function label(): string
    {
        return match($this) {
            self::MONTHLY => 'Monthly',
            self::END => 'At Maturity',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}