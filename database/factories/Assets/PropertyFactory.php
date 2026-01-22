<?php

namespace Database\Factories\Assets;

use App\Models\assets\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\assets\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    private static array $saudiCities = [
        'الرياض' => ['العليا', 'النخيل', 'الملقا', 'حي الياسمين', 'حي الربيع', 'حي السفارات'],
        'جدة' => ['الروضة', 'الحمراء', 'الشاطئ', 'حي الزهراء', 'حي السلامة', 'حي الأندلس'],
        'الدمام' => ['الفيصلية', 'الشاطئ الغربي', 'حي الجلوية', 'حي المريكبات', 'حي الأمل'],
        'مكة المكرمة' => ['العزيزية', 'الشوقية', 'الرصيفة', 'حي الكعكية'],
        'المدينة المنورة' => ['حي العوالي', 'حي الملك فهد', 'حي الدفاع'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = $this->faker->randomElement(array_keys(self::$saudiCities));
        $neighborhood = $this->faker->randomElement(self::$saudiCities[$city]);

        return [
            'country' => 'السعودية',
            'city' => $city,
            'neighborhood' => $neighborhood,
            'url_location' => 'https://maps.google.com/?q=' . $this->faker->latitude(21, 27) . ',' . $this->faker->longitude(39, 50),
            'area' => $this->faker->randomFloat(2, 500, 5000),
        ];
    }

    public function inRiyadh(): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => 'الرياض',
            'neighborhood' => $this->faker->randomElement(self::$saudiCities['الرياض']),
        ]);
    }

    public function inJeddah(): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => 'جدة',
            'neighborhood' => $this->faker->randomElement(self::$saudiCities['جدة']),
        ]);
    }

    public function inDammam(): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => 'الدمام',
            'neighborhood' => $this->faker->randomElement(self::$saudiCities['الدمام']),
        ]);
    }
}
