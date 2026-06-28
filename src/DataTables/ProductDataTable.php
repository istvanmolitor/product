<?php

declare(strict_types=1);

namespace Molitor\Product\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Molitor\Admin\DataTables\DataTable;
use Molitor\Product\Http\Resources\ProductResource;
use Molitor\Product\Models\Product;

class ProductDataTable extends DataTable
{
    protected function getModelClass(): string
    {
        return Product::class;
    }

    protected function getResourceClass(): string
    {
        return ProductResource::class;
    }

    protected function getSearchPlaceholder(): string
    {
        return 'Keresés SKU vagy név alapján...';
    }

    protected function initColumns(): void
    {
        $this->addColumn('sku')
            ->setLabel('SKU')
            ->setSearchable()
            ->setOrderable();

        $this->addColumn('name')
            ->setLabel('Név')
            ->setSearchable()
            ->setOrderable();
    }

    public function query(Builder $query): Builder
    {
        return $query
            ->joinTranslation()
            ->selectBase()
            ->with(['productUnit', 'translations']);
    }
}
