<?php

namespace App\Enums;

enum InterestPayoutOptionEnum: string
{
    case TRANSFER_TO_SAVINGS = 'TRANSFER_TO_SAVINGS';
    case RENEW_FD = 'RENEW_FD';

    public function label(): string
    {
        return match($this) {
            self::TRANSFER_TO_SAVINGS => 'Transfer to Savings',
            self::RENEW_FD => 'Renew Fixed Deposit',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}