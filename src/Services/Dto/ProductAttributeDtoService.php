<?php

namespace Molitor\Product\Services\Dto;

use Molitor\Product\Dto\ProductAttributeDto;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductAttribute;
use Molitor\Product\Repositories\ProductAttributeRepositoryInterface;

class ProductAttributeDtoService
{
    public function __construct(
        private ProductAttributeRepositoryInterface $productAttributeRepository,
        private ProductFieldDtoService $productFieldDtoService,
        private ProductFieldOptionDtoService $productFieldOptionDtoService
    )
    {
    }

    public function makeDto(ProductAttribute $productAttribute): ProductAttributeDto
    {
        $productAttributeDto = new ProductAttributeDto(
            $this->productFieldDtoService->makeDto($productAttribute->productFieldOption->productField),
            $this->productFieldOptionDtoService->makeDto($productAttribute->productFieldOption)
        );
        $productAttributeDto->sort = $productAttribute->sort;
        return $productAttributeDto;
    }

    public function saveDto(Product $product, ProductAttributeDto $productAttributeDto): ProductAttribute
    {
        $productField = $this->productFieldDtoService->saveDto($productAttributeDto->field);
        $productFieldOption = $this->productFieldOptionDtoService->saveDto($productField, $productAttributeDto->option);
        return $this->productAttributeRepository->save($product, $productFieldOption, $productAttributeDto->sort);
    }

    public function updateProductAttributes(Product $product): void
    {
        $this->productAttributeRepository->deleteAttributesByProduct($product);

        /** @var ProductAttribute $productAttribute */
        foreach ($product->productAttributes as $productAttribute) {
            $this->saveDto($product, $this->makeDto($productAttribute));
        }
    }
}
