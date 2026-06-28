<?php

declare(strict_types=1);

namespace Molitor\Product\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Molitor\Admin\DataTables\DataTable;
use Molitor\Product\Http\Resources\ProductResource;
use Molitor\Product\Models\Product;

class ProductSelectDataTable extends DataTable
{
    protected function getModelClass(): string
    {
        return Product::class;
    }

    protected function getResourceClass(): string
    {
        return ProductResource::class;
    }

    protected function initColumns(): void
    {
        $this->addColumn('sku')
            ->setLabel('SKU')
            ->setSearchable()
            ->setOrderable();

        $this->addColumn('name')
            ->setLabel('Név')
            ->setSearchable();
    }

    public function query(Builder $query): Builder
    {
        return $query
            ->joinTranslation()
            ->selectBase()
            ->with(['productUnit', 'translations', 'productImages', 'productCategories']);
    }

    protected function getDefaultSort(): string
    {
        return 'sku';
    }

    protected function getPerPage(): int
    {
        return min(50, max(1, $this->request->integer('per_page', 20)));
    }
}
