<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Support\LazyCollection;
use Molitor\Currency\Models\Currency;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductUnit;

interface ProductRepositoryInterface
{
    public function getById(int $id): Product|null;
    public function getBySku(string $sku): ?Product;

    public function findOrCreate(string $sku, string $name);

    public function save(
        string      $sku,
        string      $name,
        string      $description = null,
        float       $price = null,
        Currency    $currency = null,
        ProductUnit $productUnit = null
    ): Product;

    public function delete(Product $product): bool;

    public function getOptions(): array;

    public function getAll(): LazyCollection;
}
