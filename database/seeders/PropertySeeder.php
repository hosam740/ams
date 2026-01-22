<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use App\Models\assets\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUser = User::where('email', 'test@gmail.com')->first();

        // Property 1: الرياض
        $property1 = Property::firstOrCreate(
            ['city' => 'الرياض', 'neighborhood' => 'حي العليا'],
            [
                'country' => 'السعودية',
                'url_location' => 'https://maps.google.com/?q=24.7136,46.6753',
                'area' => 2500.00,
            ]
        );
        Asset::firstOrCreate(
            ['name' => 'برج النخيل'],
            [
                'manager_id' => $demoUser->id,
                'assetable_type' => Property::class,
                'assetable_id' => $property1->id,
            ]
        );

        // Property 2: جدة
        $property2 = Property::firstOrCreate(
            ['city' => 'جدة', 'neighborhood' => 'حي الروضة'],
            [
                'country' => 'السعودية',
                'url_location' => 'https://maps.google.com/?q=21.5433,39.1728',
                'area' => 1800.00,
            ]
        );
        Asset::firstOrCreate(
            ['name' => 'مجمع الخليج'],
            [
                'manager_id' => $demoUser->id,
                'assetable_type' => Property::class,
                'assetable_id' => $property2->id,
            ]
        );

        // Property 3: الدمام
        $property3 = Property::firstOrCreate(
            ['city' => 'الدمام', 'neighborhood' => 'حي الفيصلية'],
            [
                'country' => 'السعودية',
                'url_location' => 'https://maps.google.com/?q=26.4207,50.0888',
                'area' => 3200.00,
            ]
        );
        Asset::firstOrCreate(
            ['name' => 'مبنى الواحة'],
            [
                'manager_id' => $demoUser->id,
                'assetable_type' => Property::class,
                'assetable_id' => $property3->id,
            ]
        );

        // Property 4: مكة المكرمة
        $property4 = Property::firstOrCreate(
            ['city' => 'مكة المكرمة', 'neighborhood' => 'حي العزيزية'],
            [
                'country' => 'السعودية',
                'url_location' => 'https://maps.google.com/?q=21.4225,39.8262',
                'area' => 1500.00,
            ]
        );
        Asset::firstOrCreate(
            ['name' => 'أبراج الحرم'],
            [
                'manager_id' => $demoUser->id,
                'assetable_type' => Property::class,
                'assetable_id' => $property4->id,
            ]
        );

        // Property 5: المدينة المنورة
        $property5 = Property::firstOrCreate(
            ['city' => 'المدينة المنورة', 'neighborhood' => 'حي العوالي'],
            [
                'country' => 'السعودية',
                'url_location' => 'https://maps.google.com/?q=24.4539,39.5775',
                'area' => 2200.00,
            ]
        );
        Asset::firstOrCreate(
            ['name' => 'مجمع المدينة التجاري'],
            [
                'manager_id' => $demoUser->id,
                'assetable_type' => Property::class,
                'assetable_id' => $property5->id,
            ]
        );
    }
}
