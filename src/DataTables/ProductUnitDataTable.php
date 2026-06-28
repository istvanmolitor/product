<?php

declare(strict_types=1);

namespace Molitor\Product\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Molitor\Admin\DataTables\DataTable;
use Molitor\Product\Http\Resources\ProductUnitResource;
use Molitor\Product\Models\ProductUnit;

class ProductUnitDataTable extends DataTable
{
    protected function getModelClass(): string
    {
        return ProductUnit::class;
    }

    protected function getResourceClass(): string
    {
        return ProductUnitResource::class;
    }

    protected function getSearchPlaceholder(): string
    {
        return 'Keresés kód vagy név alapján...';
    }

    protected function initColumns(): void
    {
        $this->addColumn('code')
            ->setLabel('Kód')
            ->setSearchable()
            ->setOrderable();

        $this->addColumn('name')
            ->setLabel('Név')
            ->setOrderable();

        $this->addColumn('short_name')
            ->setLabel('Rövid név');

        $this->addColumn('enabled')
            ->setLabel('Státusz');
    }

    public function query(Builder $query): Builder
    {
        return $query->with('translations');
    }
}
