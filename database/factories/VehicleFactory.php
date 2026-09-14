<?php

namespace Database\Factories;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Models\Branch;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $type = fake()->randomElement(VehicleType::cases());

        [$make, $model, $specs] = $this->specsFor($type);

        return [
            'branch_id' => Branch::factory(),
            'type' => $type,
            'status' => VehicleStatus::Available,
            'make' => $make,
            'model' => $model,
            'year' => fake()->numberBetween(2018, 2025),
            'plate_number' => strtoupper(fake()->bothify('? #### ??')),
            'color' => fake()->randomElement(['Hitam', 'Putih', 'Silver', 'Merah', 'Biru']),
            'attributes_json' => $specs,
            'photos' => [],
            'daily_rate' => fake()->numberBetween(200_000, 800_000),
            'weekly_rate' => fake()->numberBetween(1_200_000, 5_000_000),
            'monthly_rate' => fake()->numberBetween(4_500_000, 18_000_000),
            'notes' => null,
        ];
    }

    public function car(): static
    {
        return $this->state(fn () => ['type' => VehicleType::Car]);
    }

    public function suv(): static
    {
        return $this->state(fn () => ['type' => VehicleType::Suv]);
    }

    public function motorcycle(): static
    {
        return $this->state(fn () => ['type' => VehicleType::Motorcycle]);
    }

    public function unavailable(): static
    {
        return $this->state(fn () => ['status' => VehicleStatus::Maintenance]);
    }

    /**
     * @return array{0:string,1:string,2:array<string,mixed>}
     */
    private function specsFor(VehicleType $type): array
    {
        return match ($type) {
            VehicleType::Car => [
                'Toyota', 'Avanza',
                ['seats' => 7, 'transmission' => 'manual', 'fuel' => 'bensin', 'doors' => 5, 'ac' => true],
            ],
            VehicleType::Suv => [
                'Toyota', 'Innova Reborn',
                ['seats' => 7, 'transmission' => 'manual', 'fuel' => 'diesel', 'doors' => 5, 'ac' => true],
            ],
            VehicleType::Motorcycle => [
                'Honda', 'Vario 125',
                ['engine_cc' => 125, 'transmission' => 'scooter', 'helmet' => true],
            ],
        };
    }
}
