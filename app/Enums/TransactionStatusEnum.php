<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case COMPLETED = 'COMPLETED';
    case PENDING = 'PENDING';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match($this) {
            self::COMPLETED => 'Completed',
            self::PENDING => 'Pending',
            self::FAILED => 'Failed',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}