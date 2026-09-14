<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '+62 ' . fake()->numerify('8##-####-####'),
            'whatsapp' => '62' . fake()->numerify('8##########'),
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
            'notes' => null,
        ];
    }
}
