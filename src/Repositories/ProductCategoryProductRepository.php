<?php

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductCategory;
use Molitor\Product\Models\ProductCategoryProduct;

class ProductCategoryProductRepository implements ProductCategoryProductRepositoryInterface
{
    private ProductCategoryProduct $productCategoryProduct;

    public function __construct()
    {
        $this->productCategoryProduct = new ProductCategoryProduct();
    }

    public function set(ProductCategory $productCategory, Product $product, bool $value): void
    {
        $exists = $this->exists($productCategory, $product);
        if ($value !== $exists) {
            if ($value) {
                $this->productCategoryProduct->insert(
                    [
                        'product_category_id' => $productCategory->id,
                        'product_id' => $product->id,
                    ]
                );
            } else {
                $this->productCategoryProduct
                    ->where('product_category_id', $productCategory->id)
                    ->where('product_id', $product->id)
                    ->delete();
            }
        }
    }

    public function exists(ProductCategory $productCategory, Product $product): bool
    {
        return $this->productCategoryProduct
                ->where('product_category_id', $productCategory->id)
                ->where('product_id', $product->id)
                ->count() > 0;
    }

    public function setProductCategories(Product $product, array $productCategoryIds): void
    {
        $productCategory = new ProductCategory();
        $productCategory->id = 0;

        $this->set($productCategory, $product, in_array(0, $productCategoryIds));
        foreach ((new ProductCategoryRepository())->getAll() as $productCategory) {
            $this->set($productCategory, $product, in_array($productCategory->id, $productCategoryIds));
        }
    }

    public function getProductCategoryIdsBYProduct(Product $product): array
    {
        return $this->productCategoryProduct->where('product_id', $product->id)->pluck('product_category_id')->toArray();
    }

    public function getByProduct(Product $product): Collection
    {
        return $this->productCategoryProduct->where('product_id', $product->id)->get();
    }

    public function deleteByProductCategory(ProductCategory $productCategory): void
    {
        $this->productCategoryProduct->where('product_category_id', $productCategory->id)->delete();
    }

    public function deleteByProduct(Product $product): void
    {
        $this->productCategoryProduct->where('product_id', $product->id)->delete();
    }
}
