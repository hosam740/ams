<?php

namespace Database\Seeders;

use App\Models\assets\Property;
use App\Models\assets\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::all();

        // Property 1 (الرياض - برج النخيل): 3 وحدات
        $property1 = $properties->where('city', 'الرياض')->first();
        if ($property1) {
            Unit::firstOrCreate(
                ['property_id' => $property1->id, 'name' => 'شقة 101'],
                ['type' => 'apartment', 'description' => 'شقة سكنية فاخرة، إطلالة ممتازة، تشطيب حديث', 'area' => 150.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property1->id, 'name' => 'شقة 102'],
                ['type' => 'apartment', 'description' => 'شقة عائلية واسعة، قريبة من الخدمات', 'area' => 180.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property1->id, 'name' => 'مكتب 201'],
                ['type' => 'office', 'description' => 'مكتب إداري، تجهيزات حديثة، صالة استقبال', 'area' => 80.00, 'status' => 'available']
            );
        }

        // Property 2 (جدة - مجمع الخليج): 3 وحدات
        $property2 = $properties->where('city', 'جدة')->first();
        if ($property2) {
            Unit::firstOrCreate(
                ['property_id' => $property2->id, 'name' => 'محل 1'],
                ['type' => 'store', 'description' => 'محل تجاري، واجهة زجاجية، موقع مميز', 'area' => 60.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property2->id, 'name' => 'محل 2'],
                ['type' => 'store', 'description' => 'محل تجاري، مدخل رئيسي، مناسب للمحلات', 'area' => 45.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property2->id, 'name' => 'شقة 301'],
                ['type' => 'apartment', 'description' => 'شقة سكنية، تكييف مركزي، إطلالة بحرية', 'area' => 120.00, 'status' => 'available']
            );
        }

        // Property 3 (الدمام - مبنى الواحة): 5 وحدات
        $property3 = $properties->where('city', 'الدمام')->first();
        if ($property3) {
            Unit::firstOrCreate(
                ['property_id' => $property3->id, 'name' => 'مكتب 101'],
                ['type' => 'office', 'description' => 'مكتب تجاري، موقع استراتيجي، قاعة اجتماعات', 'area' => 100.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property3->id, 'name' => 'مخزن 1'],
                ['type' => 'warehouse', 'description' => 'مخزن كبير، ارتفاع عالي، باب للشاحنات', 'area' => 350.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property3->id, 'name' => 'شقة 201'],
                ['type' => 'apartment', 'description' => 'شقة عائلية، 3 غرف نوم، مطبخ واسع', 'area' => 160.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property3->id, 'name' => 'مكتب 102'],
                ['type' => 'office', 'description' => 'مكتب إداري مجهز، مساحة مفتوحة', 'area' => 85.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property3->id, 'name' => 'شقة 202'],
                ['type' => 'apartment', 'description' => 'شقة سكنية، تشطيب ديلوكس، بلكونة واسعة', 'area' => 140.00, 'status' => 'available']
            );
        }

        // Property 4 (مكة المكرمة - أبراج الحرم): 4 وحدات
        $property4 = $properties->where('city', 'مكة المكرمة')->first();
        if ($property4) {
            Unit::firstOrCreate(
                ['property_id' => $property4->id, 'name' => 'شقة 401'],
                ['type' => 'apartment', 'description' => 'شقة مفروشة، قريبة من الحرم، إطلالة مميزة', 'area' => 100.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property4->id, 'name' => 'شقة 402'],
                ['type' => 'apartment', 'description' => 'شقة عائلية، 4 غرف، مفروشة بالكامل', 'area' => 130.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property4->id, 'name' => 'شقة 403'],
                ['type' => 'apartment', 'description' => 'شقة صغيرة، مناسبة للعزاب، مفروشة', 'area' => 70.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property4->id, 'name' => 'محل 1'],
                ['type' => 'store', 'description' => 'محل تجاري، موقع حيوي، واجهة رئيسية', 'area' => 50.00, 'status' => 'available']
            );
        }

        // Property 5 (المدينة المنورة - مجمع المدينة التجاري): 4 وحدات
        $property5 = $properties->where('city', 'المدينة المنورة')->first();
        if ($property5) {
            Unit::firstOrCreate(
                ['property_id' => $property5->id, 'name' => 'محل 10'],
                ['type' => 'store', 'description' => 'محل تجاري كبير، موقع استراتيجي، مدخلين', 'area' => 80.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property5->id, 'name' => 'محل 11'],
                ['type' => 'store', 'description' => 'محل تجاري، مناسب للمطاعم والكافيهات', 'area' => 70.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property5->id, 'name' => 'مكتب 301'],
                ['type' => 'office', 'description' => 'مكتب تجاري، الدور الثالث، مجهز بالكامل', 'area' => 120.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property5->id, 'name' => 'مخزن 2'],
                ['type' => 'warehouse', 'description' => 'مستودع كبير، سهولة الوصول، نظام أمان', 'area' => 400.00, 'status' => 'available']
            );
        }

        // وحدات إضافية للعقار 1 (الرياض)
        if ($property1) {
            Unit::firstOrCreate(
                ['property_id' => $property1->id, 'name' => 'شقة 103'],
                ['type' => 'apartment', 'description' => 'شقة دوبلكس، دورين، 5 غرف نوم', 'area' => 250.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property1->id, 'name' => 'مكتب 202'],
                ['type' => 'office', 'description' => 'مكتب إداري راقي، إطلالة بانورامية', 'area' => 95.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property1->id, 'name' => 'شقة 104'],
                ['type' => 'apartment', 'description' => 'شقة سكنية، تشطيب سوبر لوكس، سطح خاص', 'area' => 200.00, 'status' => 'available']
            );
        }

        // وحدات إضافية للعقار 2 (جدة)
        if ($property2) {
            Unit::firstOrCreate(
                ['property_id' => $property2->id, 'name' => 'محل 3'],
                ['type' => 'store', 'description' => 'معرض تجاري، مساحة كبيرة، موقع مميز', 'area' => 100.00, 'status' => 'available']
            );
            Unit::firstOrCreate(
                ['property_id' => $property2->id, 'name' => 'شقة 302'],
                ['type' => 'apartment', 'description' => 'شقة بنتهاوس، طابق كامل، إطلالة بحرية', 'area' => 220.00, 'status' => 'available']
            );
        }
    }
}
