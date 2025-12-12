<?php

namespace Molitor\Product\database\seeders;

use Illuminate\Database\Seeder;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Models\ProductFieldOption;

class ProductFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!app()->isLocal()) {
            return;
        }

        $data = require_once(__DIR__ . '/data/product_fields.php');

        foreach ($data as $productFieldData) {
            $productField = new ProductField();
            foreach ($productFieldData['name'] as $locale => $name) {
                $productField->setAttributeTranslation('name', $name, $locale);
            }
            $productField->save();

            foreach ($productFieldData['options'] as $option) {
                $productFieldOption = new ProductFieldOption();
                $productFieldOption->product_field_id = $productField->id;
                foreach ($option['name'] as $locale => $name) {
                    $productFieldOption->setAttributeTranslation('name', $name, $locale);
                }
                $productFieldOption->save();
            }
        }
    }
}
