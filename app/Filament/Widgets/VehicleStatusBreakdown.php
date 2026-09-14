<?php

namespace App\Filament\Widgets;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Filament\Widgets\Widget;

class VehicleStatusBreakdown extends Widget
{
    protected static ?int $sort = 4;

    protected static string $view = 'filament.widgets.vehicle-status-breakdown';

    /**
     * Full width on every breakpoint — fleet status is always a wide row.
     */
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('filament.Fleet Status');
    }

    /**
     * @return array<int, array{status: string, label: string, count: int, percentage: float, color: string}>
     */
    public function getStatuses(): array
    {
        $total = Vehicle::query()->count();

        if ($total === 0) {
            return [];
        }

        $counts = Vehicle::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $rows = [];

        foreach (VehicleStatus::cases() as $status) {
            $count = (int) ($counts[$status->value] ?? 0);
            $rows[] = [
                'status' => $status->value,
                'label' => $status->label(),
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 1),
                'color' => $this->statusColor($status),
            ];
        }

        return $rows;
    }

    public function getTotal(): int
    {
        return Vehicle::query()->count();
    }

    private function statusColor(VehicleStatus $status): string
    {
        return match ($status) {
            VehicleStatus::Available => 'bg-sage-400',
            VehicleStatus::OnRent => 'bg-amber-400',
            VehicleStatus::Maintenance => 'bg-surface-400',
            VehicleStatus::OutOfService => 'bg-rust-400',
            VehicleStatus::ReservedHold => 'bg-clay-400',
        };
    }
}
