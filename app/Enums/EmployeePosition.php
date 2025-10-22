<?php

namespace App\Enums;

enum EmployeePosition: string
{
    case MANAGER = 'manager';
    case BRANCH_MANAGER = 'branch_manager';
    case AGENT = 'agent';

    /**
     * Get the display label for the position
     */
    public function label(): string
    {
        return match($this) {
            self::MANAGER => 'Manager',
            self::BRANCH_MANAGER => 'Branch Manager',
            self::AGENT => 'Agent',
        };
    }

    /**
     * Get all position values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all positions as label => value pairs
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn($case) => [
                'label' => $case->label(),
                'value' => $case->value,
            ])
            ->toArray();
    }

}
