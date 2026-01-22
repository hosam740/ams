<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Tenant;
use App\Models\assets\Unit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::all();
        $tenants = Tenant::all();

        // Skip if contracts already exist
        if (Contract::count() >= 6) {
            // إذا كان هناك 6 عقود أو أكثر، نضيف فقط العقود الجديدة
            $this->addAdditionalContracts($units, $tenants);
            return;
        }

        // Contract 1: Active - شقة 101 (monthly)
        Contract::create([
            'tenant_id' => $tenants[0]->id,
            'unit_id' => $units[0]->id,
            'beginning_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonths(9)->format('Y-m-d'),
            'total_amount' => 36000,
            'payment_plan' => 'monthly',
            'status' => 'active',
        ]);

        // Contract 2: Active - مكتب 201 (quarterly)
        Contract::create([
            'tenant_id' => $tenants[1]->id,
            'unit_id' => $units[2]->id,
            'beginning_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonths(10)->format('Y-m-d'),
            'total_amount' => 48000,
            'payment_plan' => 'quarterly',
            'status' => 'active',
        ]);

        // Contract 3: Pending - محل 1 (semiannual)
        Contract::create([
            'tenant_id' => $tenants[2]->id,
            'unit_id' => $units[3]->id,
            'beginning_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonths(12)->addDays(15)->format('Y-m-d'),
            'total_amount' => 60000,
            'payment_plan' => 'semiannual',
            'status' => 'pending',
        ]);

        // Contract 4: Pending - شقة 301 (annually)
        Contract::create([
            'tenant_id' => $tenants[3]->id,
            'unit_id' => $units[5]->id,
            'beginning_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonths(12)->addDays(30)->format('Y-m-d'),
            'total_amount' => 24000,
            'payment_plan' => 'annually',
            'status' => 'pending',
        ]);

        // Contract 5: Expired - شقة 102 (monthly) - old contract
        Contract::create([
            'tenant_id' => $tenants[4]->id,
            'unit_id' => $units[1]->id,
            'beginning_date' => Carbon::now()->subMonths(18)->format('Y-m-d'),
            'end_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
            'total_amount' => 30000,
            'payment_plan' => 'monthly',
            'status' => 'expired',
        ]);

        // Contract 6: Expired - مكتب 101 (quarterly) - old contract
        Contract::create([
            'tenant_id' => $tenants[0]->id,
            'unit_id' => $units[6]->id,
            'beginning_date' => Carbon::now()->subMonths(24)->format('Y-m-d'),
            'end_date' => Carbon::now()->subMonths(12)->format('Y-m-d'),
            'total_amount' => 40000,
            'payment_plan' => 'quarterly',
            'status' => 'expired',
        ]);

        // Add additional contracts
        $this->addAdditionalContracts($units, $tenants);
    }

    private function addAdditionalContracts($units, $tenants): void
    {
        // Find available units (without active/pending contracts)
        $availableUnits = $units->filter(function ($unit) {
            return !Contract::where('unit_id', $unit->id)
                ->whereIn('status', ['active', 'pending'])
                ->exists();
        });

        if ($availableUnits->count() < 5) {
            return; // Not enough available units
        }

        $availableUnitsArray = $availableUnits->values()->all();

        // Contract 7: Active - وحدة جديدة (triannual - كل 4 شهور)
        if (isset($availableUnitsArray[0])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[0]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[1]->id,
                    'beginning_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(11)->format('Y-m-d'),
                    'total_amount' => 45000,
                    'payment_plan' => 'triannual',
                ]
            );
        }

        // Contract 8: Active - وحدة أخرى (monthly)
        if (isset($availableUnitsArray[1])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[1]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[2]->id,
                    'beginning_date' => Carbon::now()->subMonths(4)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(8)->format('Y-m-d'),
                    'total_amount' => 30000,
                    'payment_plan' => 'monthly',
                ]
            );
        }

        // Contract 9: Active - وحدة (quarterly)
        if (isset($availableUnitsArray[2])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[2]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[3]->id,
                    'beginning_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(10)->format('Y-m-d'),
                    'total_amount' => 54000,
                    'payment_plan' => 'quarterly',
                ]
            );
        }

        // Contract 10: Active - وحدة (semiannual)
        if (isset($availableUnitsArray[3])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[3]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[4]->id,
                    'beginning_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(11)->format('Y-m-d'),
                    'total_amount' => 42000,
                    'payment_plan' => 'semiannual',
                ]
            );
        }

        // Contract 11: Active - وحدة (annually)
        if (isset($availableUnitsArray[4])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[4]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[0]->id,
                    'beginning_date' => Carbon::now()->subMonths(5)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(7)->format('Y-m-d'),
                    'total_amount' => 60000,
                    'payment_plan' => 'annually',
                ]
            );
        }

        // Contract 12: Pending - وحدة (monthly)
        if (isset($availableUnitsArray[5])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[5]->id, 'status' => 'pending'],
                [
                    'tenant_id' => $tenants[1]->id,
                    'beginning_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(12)->addDays(20)->format('Y-m-d'),
                    'total_amount' => 36000,
                    'payment_plan' => 'monthly',
                ]
            );
        }

        // Contract 13: Pending - وحدة (quarterly)
        if (isset($availableUnitsArray[6])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[6]->id, 'status' => 'pending'],
                [
                    'tenant_id' => $tenants[2]->id,
                    'beginning_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(12)->addDays(10)->format('Y-m-d'),
                    'total_amount' => 48000,
                    'payment_plan' => 'quarterly',
                ]
            );
        }

        // Contract 14: Pending - وحدة (triannual)
        if (isset($availableUnitsArray[7])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[7]->id, 'status' => 'pending'],
                [
                    'tenant_id' => $tenants[3]->id,
                    'beginning_date' => Carbon::now()->addDays(25)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(12)->addDays(25)->format('Y-m-d'),
                    'total_amount' => 33000,
                    'payment_plan' => 'triannual',
                ]
            );
        }

        // Contract 15: Active - وحدة (monthly)
        if (isset($availableUnitsArray[8])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[8]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[4]->id,
                    'beginning_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                    'total_amount' => 27000,
                    'payment_plan' => 'monthly',
                ]
            );
        }

        // Contract 16: Active - وحدة (semiannual)
        if (isset($availableUnitsArray[9])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[9]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[0]->id,
                    'beginning_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(9)->format('Y-m-d'),
                    'total_amount' => 72000,
                    'payment_plan' => 'semiannual',
                ]
            );
        }

        // Contract 17: Pending - وحدة (annually)
        if (isset($availableUnitsArray[10])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[10]->id, 'status' => 'pending'],
                [
                    'tenant_id' => $tenants[1]->id,
                    'beginning_date' => Carbon::now()->addWeeks(2)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(12)->addWeeks(2)->format('Y-m-d'),
                    'total_amount' => 50000,
                    'payment_plan' => 'annually',
                ]
            );
        }

        // Contract 18: Active - وحدة (quarterly)
        if (isset($availableUnitsArray[11])) {
            Contract::firstOrCreate(
                ['unit_id' => $availableUnitsArray[11]->id, 'status' => 'active'],
                [
                    'tenant_id' => $tenants[2]->id,
                    'beginning_date' => Carbon::now()->subWeeks(3)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(12)->subWeeks(3)->format('Y-m-d'),
                    'total_amount' => 38000,
                    'payment_plan' => 'quarterly',
                ]
            );
        }
    }
}
