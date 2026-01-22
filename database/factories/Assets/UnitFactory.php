<?php

namespace Database\Factories\Assets;

use App\Models\assets\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\assets\Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;

    private static array $unitTypes = [
        'villa' => ['area_min' => 300, 'area_max' => 1000, 'names' => ['فيلا', 'فيلا فاخرة', 'فيلا دوبلكس']],
        'apartment' => ['area_min' => 80, 'area_max' => 250, 'names' => ['شقة', 'شقة سكنية', 'شقة عائلية']],
        'office' => ['area_min' => 50, 'area_max' => 300, 'names' => ['مكتب', 'مكتب إداري', 'مكتب تجاري']],
        'warehouse' => ['area_min' => 200, 'area_max' => 2000, 'names' => ['مخزن', 'مستودع', 'مخزن كبير']],
        'store' => ['area_min' => 30, 'area_max' => 200, 'names' => ['محل', 'محل تجاري', 'معرض']],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(array_keys(self::$unitTypes));
        $typeConfig = self::$unitTypes[$type];
        $unitNumber = $this->faker->numberBetween(1, 50);
        $baseName = $this->faker->randomElement($typeConfig['names']);

        return [
            'name' => $baseName . ' ' . $unitNumber,
            'type' => $type,
            'description' => $this->generateDescription($type),
            'area' => $this->faker->randomFloat(2, $typeConfig['area_min'], $typeConfig['area_max']),
            'status' => 'available',
        ];
    }

    private function generateDescription(string $type): string
    {
        $descriptions = [
            'villa' => ['مدخل خاص', 'حديقة واسعة', 'موقف سيارات', 'تكييف مركزي', 'مسبح خاص'],
            'apartment' => ['إطلالة ممتازة', 'تشطيب فاخر', 'قريبة من الخدمات', 'مدخل مستقل'],
            'office' => ['تجهيزات حديثة', 'موقع استراتيجي', 'صالة استقبال', 'قاعة اجتماعات'],
            'warehouse' => ['ارتفاع عالي', 'أرضية مقواة', 'باب كبير للشاحنات', 'نظام إضاءة'],
            'store' => ['واجهة زجاجية', 'موقع مميز', 'مدخل رئيسي', 'مناسب للمحلات التجارية'],
        ];

        $features = $this->faker->randomElements($descriptions[$type], $this->faker->numberBetween(2, 3));
        return implode('، ', $features);
    }

    public function apartment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'apartment',
            'name' => $this->faker->randomElement(self::$unitTypes['apartment']['names']) . ' ' . $this->faker->numberBetween(1, 50),
            'area' => $this->faker->randomFloat(2, 80, 250),
            'description' => $this->generateDescription('apartment'),
        ]);
    }

    public function office(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'office',
            'name' => $this->faker->randomElement(self::$unitTypes['office']['names']) . ' ' . $this->faker->numberBetween(1, 50),
            'area' => $this->faker->randomFloat(2, 50, 300),
            'description' => $this->generateDescription('office'),
        ]);
    }

    public function store(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'store',
            'name' => $this->faker->randomElement(self::$unitTypes['store']['names']) . ' ' . $this->faker->numberBetween(1, 50),
            'area' => $this->faker->randomFloat(2, 30, 200),
            'description' => $this->generateDescription('store'),
        ]);
    }

    public function warehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'warehouse',
            'name' => $this->faker->randomElement(self::$unitTypes['warehouse']['names']) . ' ' . $this->faker->numberBetween(1, 50),
            'area' => $this->faker->randomFloat(2, 200, 2000),
            'description' => $this->generateDescription('warehouse'),
        ]);
    }
}
