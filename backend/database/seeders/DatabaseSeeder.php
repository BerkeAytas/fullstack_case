<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductField;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::create([
            'name' => 'Test Tişört',
            'slug' => 'test-tisort',
            'price' => 199.90,
            'stock' => 10,
        ]);

        $colorField = ProductField::create(['name' => 'Renk', 'type' => 'select', 'is_variation' => true]);
        $colorOptions = $colorField->options()->createMany([
            ['label' => 'Kırmızı'],
            ['label' => 'Mavi'],
            ['label' => 'Siyah'],
        ]);

        $sizeField = ProductField::create(['name' => 'Beden', 'type' => 'radio', 'is_variation' => true]);
        $sizeOptions = $sizeField->options()->createMany([
            ['label' => 'S'],
            ['label' => 'M'],
            ['label' => 'L'],
            ['label' => 'XL'],
        ]);

        $red = $colorOptions->firstWhere('label', 'Kırmızı');
        $black = $colorOptions->firstWhere('label', 'Siyah');
        $small = $sizeOptions->firstWhere('label', 'S');
        $medium = $sizeOptions->firstWhere('label', 'M');
        $large = $sizeOptions->firstWhere('label', 'L');

        $v1 = $product->variations()->create(['sku' => 'TS-RED-S', 'price' => 199.90, 'stock' => 5]);
        $v1->options()->attach([$red->id, $small->id]);

        $v2 = $product->variations()->create(['sku' => 'TS-RED-M', 'price' => 199.90, 'stock' => 8]);
        $v2->options()->attach([$red->id, $medium->id]);

        $v3 = $product->variations()->create(['sku' => 'TS-BLACK-L', 'price' => 209.90, 'stock' => 3]);
        $v3->options()->attach([$black->id, $large->id]);
        $fabricField = ProductField::create(['name' => 'Kumaş Türü', 'type' => 'text', 'is_variation' => false]);
$product->fieldValues()->create([
    'product_field_id' => $fabricField->id,
    'value' => '%100 Pamuk',
]);
    }
}