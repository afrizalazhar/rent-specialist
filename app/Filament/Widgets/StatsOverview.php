<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\PaymentRecord;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $activeStatuses = [BookingStatus::Confirmed->value, BookingStatus::PickedUp->value];

        $onRent = Booking::query()
            ->where('status', BookingStatus::PickedUp->value)
            ->count();

        $returningToday = Booking::query()
            ->whereIn('status', $activeStatuses)
            ->whereDate('planned_return_at', today())
            ->count();

        $available = Vehicle::query()
            ->where('status', VehicleStatus::Available->value)
            ->count();

        $outstanding = $this->outstandingTotal($activeStatuses);

        return [
            Stat::make(__('filament.On Rent'), $onRent)
                ->description(__('filament.Today'))
                ->descriptionIcon('heroicon-m-clock')
                ->descriptionColor('gray')
                ->icon('heroicon-m-truck')
                ->extraAttributes(['data-accent' => 'amber']),

            Stat::make(__('filament.Returning Today'), $returningToday)
                ->description(__('filament.Today'))
                ->descriptionIcon('heroicon-m-clock')
                ->descriptionColor('gray')
                ->icon('heroicon-m-arrow-left-end-on-rectangle')
                ->extraAttributes(['data-accent' => 'clay']),

            Stat::make(__('filament.Outstanding'), self::rupiah($outstanding))
                ->description(__('filament.Today'))
                ->descriptionIcon('heroicon-m-clock')
                ->descriptionColor('gray')
                ->icon('heroicon-m-banknotes')
                ->extraAttributes(['data-accent' => 'rust']),

            Stat::make(__('filament.Available'), $available)
                ->description(__('filament.Today'))
                ->descriptionIcon('heroicon-m-clock')
                ->descriptionColor('gray')
                ->icon('heroicon-m-check-circle')
                ->extraAttributes(['data-accent' => 'sage']),
        ];
    }

    /**
     * Sum of outstandingAmount() across active bookings, computed with a
     * small grouped query instead of N+1 per-booking queries.
     *
     * @param  array<int, string>  $statuses
     */
    private function outstandingTotal(array $statuses): int
    {
        $activeIds = Booking::query()->whereIn('status', $statuses)->pluck('id');

        $paid = PaymentRecord::query()
            ->whereIn('booking_id', $activeIds)
            ->selectRaw('booking_id, SUM(amount) as total')
            ->groupBy('booking_id')
            ->pluck('total', 'booking_id');

        $charged = Booking::query()
            ->whereIn('id', $activeIds)
            ->pluck('charged_amount', 'id');

        $total = 0;

        foreach ($charged as $id => $amount) {
            $total += max(0, (int) $amount - (int) ($paid[$id] ?? 0));
        }

        return $total;
    }

    private static function rupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
