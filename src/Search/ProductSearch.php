<?php

declare(strict_types=1);

namespace Molitor\Product\Search;

use Molitor\Admin\Search\AdminSearch;
use Molitor\Admin\Search\AdminSearchResults;
use Molitor\Product\Models\Product;

class ProductSearch extends AdminSearch
{
    public function search(string $q, int $limit, AdminSearchResults $results): void
    {
        $this->filter(Product::query(), $q, ['sku'])
            ->limit($limit)
            ->get()
            ->each(fn (Product $product) => $results->addResult(
                type: 'product',
                typeLabel: 'Termék',
                id: $product->id,
                title: $product->name ?? $product->sku,
                subtitle: $product->sku,
                url: "/admin/product/products/{$product->id}",
            ));
    }
}
