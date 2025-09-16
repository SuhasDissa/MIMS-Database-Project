<?php

namespace App\Enums;

enum TransactionMethodEnum: string
{
    case ACCOUNT = 'ACCOUNT';
    case CASH = 'CASH';

    public function label(): string
    {
        return match($this) {
            self::ACCOUNT => 'Account Transfer',
            self::CASH => 'Cash',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}