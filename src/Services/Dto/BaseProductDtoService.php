<?php

namespace Molitor\Product\Services\Dto;

use Illuminate\Database\Eloquent\Model;
use Molitor\CustomerProduct\Models\CustomerProduct;
use Molitor\CustomerProduct\Models\CustomerProductImage;
use Molitor\Product\Dto\ImageDto;
use Molitor\Product\Dto\ProductDto;

abstract class BaseProductDtoService
{
    protected function saveModel(Model $model, ProductDto $productDto): void
    {
        $this->fillModel($model, $productDto);
        $model->save();
        $this->updateModelImages($model, $productDto);
    }

    protected function fillModel(Model $model, ProductDto $productDto): void
    {
        $model->sku = $productDto->sku;
        $model->setAttributeDto('name', $productDto->name);
        $model->setAttributeDto('description', $productDto->description);
        $model->price = $productDto->price;
        $model->url = $productDto->url;
        $model->product_unit_id = $this->productUnitDtoService->saveDto($productDto->productUnit)->id;
    }

    protected function updateModelImages(Model $productModel, string $imagesField, string $productIdField, ProductDto $productDto): void
    {

    }
}
