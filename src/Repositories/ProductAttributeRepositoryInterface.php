<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductAttribute;
use Molitor\Product\Models\ProductFieldOption;
use Illuminate\Database\Eloquent\Collection;

interface ProductAttributeRepositoryInterface
{

    public function setAttribute(Product $product, string $name, string|array $value): self;

    public function getProductAttributesByProduct(Product $product): Collection;

    public function deleteAttributesByProduct(Product $product): bool;

    public function delete(Product $product, ProductFieldOption $productFieldOption): self;

    public function save(Product $product, ProductFieldOption $productFieldOption, int $sort): ProductAttribute;
}
