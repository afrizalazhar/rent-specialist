<?php

namespace Database\Seeders;

use App\Enums\StaffRole;
use App\Models\Branch;
use App\Models\StaffUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['name' => 'Cabang Utama'],
            [
                'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
                'phone'   => '+62 21-1234-5678',
                'whatsapp'=> '6281234567890',
                'email'   => 'halo@rent.local',
                'hours'   => 'Senin–Minggu, 08.00–21.00',
                'map_url' => 'https://maps.google.com/?q=-6.2088,106.8456',
            ]
        );

        // Manager account
        StaffUser::firstOrCreate(
            ['email' => 'manager@rent.local'],
            [
                'name' => 'Pemilik Usaha',
                'password' => Hash::make('password'),
                'role' => StaffRole::Manager,
                'email_verified_at' => now(),
            ]
        );

        // Staff account
        StaffUser::firstOrCreate(
            ['email' => 'staff@rent.local'],
            [
                'name' => 'Staf Operasional',
                'password' => Hash::make('password'),
                'role' => StaffRole::Staff,
                'email_verified_at' => now(),
            ]
        );

        // Demo vehicles
        \App\Models\Vehicle::factory()
            ->count(4)
            ->car()
            ->create(['branch_id' => $branch->id]);

        \App\Models\Vehicle::factory()
            ->count(2)
            ->suv()
            ->create(['branch_id' => $branch->id]);

        \App\Models\Vehicle::factory()
            ->count(4)
            ->motorcycle()
            ->create(['branch_id' => $branch->id]);
    }
}
