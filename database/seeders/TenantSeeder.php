<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 5 مستأجرين بجنسيات مختلفة
        Tenant::firstOrCreate(
            ['national_id' => '1087654321'],
            [
                'first_name' => 'محمد',
                'last_name' => 'العتيبي',
                'phone_number' => '0551234567',
                'nationality' => 'سعودي',
            ]
        );

        Tenant::firstOrCreate(
            ['national_id' => '1098765432'],
            [
                'first_name' => 'أحمد',
                'last_name' => 'الشمري',
                'phone_number' => '0562345678',
                'nationality' => 'سعودي',
            ]
        );

        Tenant::firstOrCreate(
            ['national_id' => '2123456789'],
            [
                'first_name' => 'خالد',
                'last_name' => 'محمود',
                'phone_number' => '0573456789',
                'nationality' => 'مصري',
            ]
        );

        Tenant::firstOrCreate(
            ['national_id' => '2234567890'],
            [
                'first_name' => 'عبدالله',
                'last_name' => 'الأردني',
                'phone_number' => '0584567890',
                'nationality' => 'أردني',
            ]
        );

        Tenant::firstOrCreate(
            ['national_id' => '1076543210'],
            [
                'first_name' => 'سعود',
                'last_name' => 'القحطاني',
                'phone_number' => '0595678901',
                'nationality' => 'سعودي',
            ]
        );
    }
}
