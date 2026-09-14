<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\VehicleType;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = CarbonImmutable::now()->addDays(fake()->numberBetween(1, 14))->setTime(9, 0);
        $days = fake()->numberBetween(1, 5);
        $end = $start->addDays($days);

        return [
            'customer_id' => Customer::factory(),
            'vehicle_id' => Vehicle::factory(),
            'branch_id' => Branch::factory(),
            'status' => BookingStatus::Confirmed,
            'planned_pickup_at' => $start,
            'planned_return_at' => $end,
            'actual_pickup_at' => null,
            'actual_return_at' => null,
            'calculated_base_amount' => 0,
            'calculated_overage_amount' => 0,
            'charged_amount' => 0,
            'notes' => null,
        ];
    }

    public function forVehicleType(VehicleType $type): static
    {
        return $this->state(fn () => [
            'vehicle_id' => Vehicle::factory()->state(['type' => $type]),
        ]);
    }
}
