<?php

namespace Molitor\Product\Services;

use Illuminate\Database\Eloquent\Model;
use Molitor\Product\Dto\ProductDto;
use Molitor\Product\Models\Product;
use Molitor\Product\Repositories\ProductRepositoryInterface;

class ProductManager extends BaseProductManagerService
{
    public function getModelByDro(ProductDto $productDto): Model|null
    {
        return app(ProductRepositoryInterface::class)->getBySku($productDto->sku);
    }

    protected function getNewModel(): Model
    {
        return new Product();
    }

    protected function saveModelAttributeIds(Model $model, array $attributeOptionIds): void
    {

    }
}
