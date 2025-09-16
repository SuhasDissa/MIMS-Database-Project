<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case DEPOSIT = 'DEPOSIT';
    case WITHDRAWAL = 'WITHDRAWAL';
    case TRANSFER = 'TRANSFER';

    public function label(): string
    {
        return match($this) {
            self::DEPOSIT => 'Deposit',
            self::WITHDRAWAL => 'Withdrawal',
            self::TRANSFER => 'Transfer',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}