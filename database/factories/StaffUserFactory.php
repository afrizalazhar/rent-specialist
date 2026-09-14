<?php

namespace Database\Factories;

use App\Enums\StaffRole;
use App\Models\StaffUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<StaffUser>
 */
class StaffUserFactory extends Factory
{
    protected $model = StaffUser::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => StaffRole::Staff,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }

    public function manager(): static
    {
        return $this->state(fn () => ['role' => StaffRole::Manager]);
    }
}
