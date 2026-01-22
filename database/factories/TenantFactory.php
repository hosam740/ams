<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    private static array $arabicFirstNames = [
        'محمد', 'أحمد', 'علي', 'عبدالله', 'خالد', 'فهد', 'سعود', 'ناصر',
        'عبدالرحمن', 'سلطان', 'فيصل', 'تركي', 'ماجد', 'سامي', 'يوسف', 'إبراهيم'
    ];

    private static array $arabicLastNames = [
        'العتيبي', 'القحطاني', 'الدوسري', 'الشمري', 'المطيري', 'الحربي',
        'الزهراني', 'الغامدي', 'السبيعي', 'العنزي', 'الرشيدي', 'البلوي',
        'الشهري', 'العمري', 'المالكي', 'الأحمدي'
    ];

    private static array $nationalities = [
        'سعودي', 'مصري', 'أردني', 'سوري', 'يمني', 'فلسطيني', 'سوداني', 'باكستاني'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->randomElement(self::$arabicFirstNames),
            'last_name' => $this->faker->randomElement(self::$arabicLastNames),
            'national_id' => $this->generateNationalId(),
            'phone_number' => $this->generateSaudiPhone(),
            'nationality' => $this->faker->randomElement(self::$nationalities),
        ];
    }

    private function generateNationalId(): string
    {
        // 10 digits starting with 1 (Saudi) or 2 (resident)
        $prefix = $this->faker->randomElement(['1', '2']);
        return $prefix . $this->faker->numerify('#########');
    }

    private function generateSaudiPhone(): string
    {
        // Saudi mobile numbers start with 05
        return '05' . $this->faker->numerify('########');
    }

    public function saudi(): static
    {
        return $this->state(fn (array $attributes) => [
            'nationality' => 'سعودي',
            'national_id' => '1' . $this->faker->numerify('#########'),
        ]);
    }

    public function egyptian(): static
    {
        return $this->state(fn (array $attributes) => [
            'nationality' => 'مصري',
            'national_id' => '2' . $this->faker->numerify('#########'),
        ]);
    }
}
