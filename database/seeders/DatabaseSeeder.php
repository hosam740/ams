<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo user (or get existing)
        $demoUser = User::firstOrCreate(
            ['email' => 'test@gmail.com'],
            [
                'name' => 'حساب تجريبي',
                'phone_number' => '0512345678',
                'password' => Hash::make('aaaaaaaa'),
            ]
        );

        // Call seeders in order
        $this->call([
            PropertySeeder::class,
            UnitSeeder::class,
            TenantSeeder::class,
            ContractSeeder::class,
        ]);
    }
}
