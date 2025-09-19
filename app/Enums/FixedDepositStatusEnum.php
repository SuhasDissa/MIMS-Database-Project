<?php

namespace App\Enums;

enum FixedDepositStatusEnum: string
{
    case ACTIVE = 'ACTIVE';
    case MATURED = 'MATURED';
    case PREMATURELY_CLOSED = 'PREMATURELY_CLOSED';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::MATURED => 'Matured',
            self::PREMATURELY_CLOSED => 'Prematurely Closed',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}