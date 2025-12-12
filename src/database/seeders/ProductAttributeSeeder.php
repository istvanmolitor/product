<?php

namespace Molitor\Product\database\seeders;

use Illuminate\Database\Seeder;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductAttribute;
use Molitor\Product\Models\ProductFieldOption;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Randomly assign multiple attributes (one option per field) to each product.
     */
    public function run(): void
    {
        if (!app()->isLocal()) {
            return;
        }

        $products = Product::query()->get();
        if ($products->isEmpty()) {
            return;
        }

        // Load all options and group them by product_field_id
        $optionsByField = ProductFieldOption::query()->get()->groupBy('product_field_id');
        if ($optionsByField->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            // Skip products that already have attributes
            if ($product->productAttributes()->exists()) {
                continue;
            }

            // Choose 1-3 distinct fields to assign
            $fieldIds = $optionsByField->keys()->all();
            shuffle($fieldIds);
            $count = random_int(1, min(3, count($fieldIds)));
            $selectedFieldIds = array_slice($fieldIds, 0, $count);

            $sort = 1;
            foreach ($selectedFieldIds as $fieldId) {
                $options = $optionsByField->get($fieldId);
                if (!$options || $options->isEmpty()) {
                    continue;
                }

                $randomOption = $options->random();

                // Create attribute if not exists (respect composite PK uniqueness)
                ProductAttribute::query()->firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_field_option_id' => $randomOption->id,
                    ],
                    [
                        'sort' => $sort,
                    ]
                );
                $sort++;
            }
        }
    }
}
