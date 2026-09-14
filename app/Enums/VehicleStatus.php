<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Available = 'available';
    case OnRent = 'on_rent';
    case Maintenance = 'maintenance';
    case OutOfService = 'out_of_service';
    case ReservedHold = 'reserved_hold';

    public function label(): string
    {
        return match ($this) {
            self::Available     => 'Tersedia',
            self::OnRent        => 'Sedang Dirental',
            self::Maintenance   => 'Perawatan',
            self::OutOfService  => 'Tidak Aktif',
            self::ReservedHold  => 'Diblokir',
        };
    }

    /** Whether the vehicle should be shown on the public catalog. */
    public function isPubliclyVisible(): bool
    {
        return $this === self::Available;
    }

    /** Whether new bookings can be confirmed against this vehicle. */
    public function isBookable(): bool
    {
        return in_array($this, [self::Available, self::ReservedHold], true);
    }

    public function pillClass(): string
    {
        return match ($this) {
            self::Available     => 'neu-pill-success',
            self::OnRent        => 'neu-pill-warning',
            self::Maintenance   => 'neu-pill-neutral',
            self::OutOfService  => 'neu-pill-danger',
            self::ReservedHold  => 'neu-pill-neutral',
        };
    }
}
