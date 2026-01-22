<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $beginningDate = Carbon::now()->subMonths($this->faker->numberBetween(0, 6));
        $duration = $this->faker->randomElement([6, 12, 24]); // months
        $endDate = $beginningDate->copy()->addMonths($duration);

        return [
            'beginning_date' => $beginningDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_amount' => $this->faker->randomElement([24000, 36000, 48000, 60000, 72000, 120000]),
            'payment_plan' => $this->faker->randomElement(['monthly', 'quarterly', 'semiannual', 'annually']),
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $beginningDate = Carbon::now()->subMonths($this->faker->numberBetween(1, 6));
            $endDate = Carbon::now()->addMonths($this->faker->numberBetween(3, 12));

            return [
                'beginning_date' => $beginningDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'active',
            ];
        });
    }

    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            $beginningDate = Carbon::now()->addDays($this->faker->numberBetween(7, 30));
            $endDate = $beginningDate->copy()->addMonths($this->faker->randomElement([6, 12]));

            return [
                'beginning_date' => $beginningDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'pending',
            ];
        });
    }

    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            $beginningDate = Carbon::now()->subMonths($this->faker->numberBetween(12, 24));
            $endDate = Carbon::now()->subMonths($this->faker->numberBetween(1, 6));

            return [
                'beginning_date' => $beginningDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'expired',
            ];
        });
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_plan' => 'monthly',
        ]);
    }

    public function quarterly(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_plan' => 'quarterly',
        ]);
    }

    public function annually(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_plan' => 'annually',
        ]);
    }
}
