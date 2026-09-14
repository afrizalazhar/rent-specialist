<?php

namespace App\Livewire;

use App\Models\Vehicle;
use Livewire\Component;

/**
 * Read-only availability calendar for a vehicle on the public site.
 * Fetches the confirmed-booking window from the JSON endpoint and
 * paints a single-month grid. No interaction beyond month navigation.
 */
class AvailabilityCalendar extends Component
{
    public Vehicle $vehicle;
    public string $month; // YYYY-MM
    /** @var array<int,array{from:string,to:string}> */
    public array $unavailable = [];

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        $this->month = now()->format('Y-m');
        $this->load();
    }

    public function previousMonth(): void
    {
        $this->month = \Carbon\Carbon::createFromFormat('Y-m', $this->month)->subMonth()->format('Y-m');
        $this->load();
    }

    public function nextMonth(): void
    {
        $this->month = \Carbon\Carbon::createFromFormat('Y-m', $this->month)->addMonth()->format('Y-m');
        $this->load();
    }

    /** @return array<int,array{0:int,1:int,2:bool,3:bool}> rows of [isoYear, isoWeek, inMonth, isPast] */
    public function getCalendarRowsProperty(): array
    {
        $cursor = \Carbon\Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $today = now()->startOfDay();
        $start = $cursor->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $end = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $rows = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $rows[] = [
                $d->year,
                $d->month,
                $d->day,
                $d->month === $cursor->month,
                $d->lt($today),
                $this->isUnavailable($d),
            ];
        }

        return array_chunk($rows, 7);
    }

    public function getMonthLabelProperty(): string
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->month)
            ->locale('id')
            ->translatedFormat('F Y');
    }

    public function render()
    {
        return view('livewire.availability-calendar');
    }

    private function load(): void
    {
        $from = \Carbon\Carbon::createFromFormat('Y-m', $this->month)->startOfMonth()->toDateString();
        $to   = \Carbon\Carbon::createFromFormat('Y-m', $this->month)->endOfMonth()->toDateString();

        $payload = \Illuminate\Support\Facades\Http::get(
            route('vehicles.availability', ['vehicle' => $this->vehicle->id, 'from' => $from, 'to' => $to])
        )->json();

        $this->unavailable = $payload['unavailable'] ?? [];
    }

    private function isUnavailable(\Carbon\Carbon $day): bool
    {
        foreach ($this->unavailable as $range) {
            $from = \Carbon\Carbon::parse($range['from']);
            $to = \Carbon\Carbon::parse($range['to']);
            if ($day->betweenIncluded($from, $to)) {
                return true;
            }
        }
        return false;
    }
}
