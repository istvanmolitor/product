<?php

declare(strict_types=1);

namespace Molitor\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductCategory;
use Molitor\Product\Models\ProductUnit;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->words(3, true);

        return [
            'active' => $this->faker->boolean(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'description' => $this->faker->text(),
            'sku' => $this->faker->unique()->ean13(),
            'price' => $this->faker->randomFloat(2, 0, 10000),
            'product_unit_id' => ProductUnit::inRandomOrder()->value('id'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // Attach the product to 1-2 random categories if any exist
            $total = ProductCategory::count();
            if ($total === 0) {
                return;
            }
            $limit = $this->faker->numberBetween(1, min(2, $total));
            $categoryIds = ProductCategory::inRandomOrder()
                ->limit($limit)
                ->pluck('id')
                ->all();

            if (! empty($categoryIds)) {
                $product->productCategories()->syncWithoutDetaching($categoryIds);
            }
        });
    }
}
