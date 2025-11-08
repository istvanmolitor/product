<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;
use Molitor\Currency\Models\Currency;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Product\Events\ProductDestroyEvent;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductUnit;

class ProductRepository implements ProductRepositoryInterface
{
    protected Product $product;

    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository,
        private ProductUnitRepositoryInterface $productUnitRepository,
        private ProductCategoryProductRepositoryInterface $productCategoryProductRepository
    )
    {
        $this->product = new Product();
    }

    public function getById(int $id): Product|null
    {
        return $this->product->where('id', $id)->first();
    }

    public function getBySku(string $sku): ?Product
    {
        return $this->product->where('sku', $sku)->first();
    }

    public function findOrCreate(string $sku, string $name)
    {
        $product = $this->getBySku($sku);
        if ($product) {
            return $product;
        }
        $currency = $this->currencyRepository->getByCode('HUF');
        $productUnit = $this->productUnitRepository->getDefault();
        return $this->product->create(
            [
                'active' => false,
                'sku' => $sku,
                'name' => $name,
                'currency_id' => $currency->id,
                'product_unit_id' => $productUnit->id,
            ]
        );
    }

    public function save(
        string      $sku,
        string      $name,
        string      $description = null,
        float       $price = null,
        Currency    $currency = null,
        ProductUnit $productUnit = null
    ): Product
    {
        $currency = $this->currencyRepository->make($currency);
        $productUnit = $this->productUnitRepository->make($productUnit);

        $product = $this->getBySku($sku);
        if ($product) {
            $product->name = $name;
            if (!empty($description)) {
                $product->description = $description;
            }
            if ($price !== null) {
                $product->price = $price;
            }

            $product->save();

        } else {
            if ($price === null) {
                // Ensure price is always set when creating a product
                $price = 0;
            }
            $product = $this->product->create(
                [
                    'active' => false,
                    'sku' => $sku,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'currency_id' => $currency->id,
                    'product_unit_id' => $productUnit->id,
                ]
            );
        }

        return $product;
    }

    public function delete(Product $product): bool
    {
        event(new ProductDestroyEvent($product));
        $this->productCategoryProductRepository->deleteByProduct($product);
        return $product->delete();
    }

    public function getFileData(): Collection
    {
        return $this->product
            ->orderBy('sku')
            ->select(['sku', 'name', 'description', 'price'])
            ->with('currency')
            ->get();
    }

    public function getOptions(): array
    {
        return $this->product->orderBy('sku')->pluck('sku', 'id')->toArray();
    }

    public function getAll(): LazyCollection
    {
        return $this->product->orderBy('sku')->cursor();
    }
}
