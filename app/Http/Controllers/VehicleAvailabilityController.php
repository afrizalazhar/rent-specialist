<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleAvailabilityController extends Controller
{
    /**
     * Returns the [start, end] date ranges where this vehicle is unavailable
     * because of a confirmed booking. Used by the public calendar.
     */
    public function show(Request $request, Vehicle $vehicle): JsonResponse
    {
        $from = $request->query('from')
            ? CarbonImmutable::parse($request->query('from'))->startOfDay()
            : CarbonImmutable::today();
        $to = $request->query('to')
            ? CarbonImmutable::parse($request->query('to'))->endOfDay()
            : $from->addMonths(3);

        $bookings = $vehicle->bookings()
            ->whereIn('status', [
                BookingStatus::Confirmed->value,
                BookingStatus::PickedUp->value,
            ])
            ->where('planned_pickup_at', '<', $to)
            ->where('planned_return_at', '>', $from)
            ->orderBy('planned_pickup_at')
            ->get(['planned_pickup_at', 'planned_return_at']);

        return response()->json([
            'vehicle_id' => $vehicle->id,
            'unavailable' => $bookings->map(fn ($b) => [
                'from' => $b->planned_pickup_at->toDateString(),
                'to'   => $b->planned_return_at->toDateString(),
            ])->all(),
        ]);
    }
}
