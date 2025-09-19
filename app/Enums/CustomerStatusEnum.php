<?php

namespace App\Enums;

enum CustomerStatusEnum: string
{
    case CHILD = 'CHILD';
    case SENIOR = 'SENIOR';
    case ADULT = 'ADULT';

    public function label(): string
    {
        return match($this) {
            self::CHILD => 'Child',
            self::SENIOR => 'Senior',
            self::ADULT => 'Adult',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}