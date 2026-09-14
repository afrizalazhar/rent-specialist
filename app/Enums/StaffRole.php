<?php

namespace App\Enums;

enum StaffRole: string
{
    case Manager = 'manager';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Manajer',
            self::Staff   => 'Staf',
        };
    }

    public function canManageStaff(): bool
    {
        return $this === self::Manager;
    }

    public function canEditSettings(): bool
    {
        return $this === self::Manager;
    }
}
