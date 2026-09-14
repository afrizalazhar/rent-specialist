<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => 'Cabang ' . fake()->city(),
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
            'phone' => '+62 ' . fake()->numerify('8##-####-####'),
            'whatsapp' => '62' . fake()->numerify('8##########'),
            'email' => fake()->safeEmail(),
            'hours' => 'Senin–Minggu, 08.00–21.00',
            'map_url' => 'https://maps.google.com/?q=' . urlencode(fake()->city()),
        ];
    }
}
