<?php

namespace Molitor\Product\Services\Dto;

use Molitor\Product\Dto\ProductFieldDto;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Repositories\ProductFieldRepositoryInterface;

class ProductFieldDtoService
{
    public function __construct(
        protected ProductFieldRepositoryInterface $productFieldRepository,
    )
    {
    }

    public function makeDto(ProductField $productField): ProductFieldDto
    {
        $attributeDto = new ProductFieldDto();
        if($productField->exists) {
            $attributeDto->id = $productField->id;
        }
        $attributeDto->multiple = $productField->multiple;
        $attributeDto->name = $productField->getAttributeDto('name');
        $attributeDto->description = $productField->getAttributeDto('description');
        return $attributeDto;
    }

    public function saveDto(ProductFieldDto $productFieldDto): ProductField
    {
        $productField = $this->makeModel($productFieldDto);
        $this->fillModel($productField, $productFieldDto);
        $productField->save();
        return $productField;
    }

    public function makeModel(ProductFieldDto $productFieldDto): ProductField
    {
        if ($productFieldDto->id) {
            $productField = $this->productFieldRepository->getById($productFieldDto->id);
            if ($productField) {
                return $productField;
            }
        }

        $productField = $this->productFieldRepository->getByMultilingualName($productFieldDto->name);
        if ($productField) {
            return $productField;
        }

        return new ProductField();
    }

    public function fillModel(ProductField $productField, ProductFieldDto $productFieldDto): void
    {
        $productField->setAttributeDto('name', $productFieldDto->name);
        $productField->setAttributeDto('description', $productFieldDto->description);
        $productField->setAttribute('multiple', $productFieldDto->multiple);
    }
}
