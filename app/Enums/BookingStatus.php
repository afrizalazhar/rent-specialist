<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case PickedUp = 'picked_up';
    case Returned = 'returned';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft       => 'Draf',
            self::Confirmed   => 'Dikonfirmasi',
            self::PickedUp    => 'Diambil',
            self::Returned    => 'Dikembalikan',
            self::Closed      => 'Selesai',
            self::Cancelled   => 'Dibatalkan',
        };
    }

    public function pillClass(): string
    {
        return match ($this) {
            self::Draft, self::Confirmed => 'neu-pill-neutral',
            self::PickedUp, self::Returned => 'neu-pill-warning',
            self::Closed => 'neu-pill-success',
            self::Cancelled => 'neu-pill-danger',
        };
    }

    /** Statuses the booking can move to from this one. */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft      => [self::Confirmed, self::Cancelled],
            self::Confirmed  => [self::PickedUp, self::Cancelled],
            self::PickedUp   => [self::Returned, self::Cancelled],
            self::Returned   => [self::Closed],
            self::Closed, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }
}
