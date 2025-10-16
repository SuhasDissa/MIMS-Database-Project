<?php

namespace App\Enums;

enum CustomerStatusEnum: string
{
    case CHILD = 'CHILD';
    case TEEN = 'TEEN';
    case ADULT = 'ADULT';
    case SENIOR = 'SENIOR';

    public function label(): string
    {
        return match($this) {
            self::CHILD => 'Child',
            self::TEEN => 'Teen',
            self::ADULT => 'Adult',
            self::SENIOR => 'Senior',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}