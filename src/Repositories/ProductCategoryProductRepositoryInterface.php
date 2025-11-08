<?php

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductCategory;

interface ProductCategoryProductRepositoryInterface
{
    public function set(ProductCategory $productCategory, Product $product, bool $value): void;

    public function exists(ProductCategory $productCategory, Product $product): bool;

    public function setProductCategories(Product $product, array $productCategoryIds): void;

    public function getProductCategoryIdsBYProduct(Product $product): array;

    public function getByProduct(Product $product): Collection;

    public function deleteByProductCategory(ProductCategory $productCategory): void;

    public function deleteByProduct(Product $product): void;
}
