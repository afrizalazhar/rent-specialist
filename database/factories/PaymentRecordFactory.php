<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\PaymentRecord;
use App\Models\StaffUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentRecord>
 */
class PaymentRecordFactory extends Factory
{
    protected $model = PaymentRecord::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => fake()->numberBetween(100_000, 1_000_000),
            'received_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'method' => fake()->randomElement(PaymentMethod::cases()),
            'reference' => null,
            'notes' => null,
            'recorded_by' => StaffUser::factory(),
        ];
    }
}
