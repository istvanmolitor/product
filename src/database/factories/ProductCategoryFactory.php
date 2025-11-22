<?php

declare(strict_types=1);

namespace Molitor\Product\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Molitor\Product\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'parent_id' => 0,
            'left_value' => null,
            'right_value' => null,
            'image' => null,
            'image_url' => null,

            // Translatable fields (handled by TranslatableModel)
            'name' => $this->faker->unique()->words(2, true),

            'description' => $this->faker->sentence(10),
        ];
    }
}
