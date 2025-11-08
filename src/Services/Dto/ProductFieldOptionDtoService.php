<?php

namespace Molitor\Product\Services\Dto;

use Molitor\Product\Dto\ProductFieldOptionDto;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Models\ProductFieldOption;
use Molitor\Product\Repositories\ProductFieldOptionRepositoryInterface;

class ProductFieldOptionDtoService
{
    public function __construct(
        protected ProductFieldOptionRepositoryInterface $productFieldOptionRepository,
    )
    {
    }

    public function makeDto(ProductFieldOption $productFieldOption): ProductFieldOptionDto
    {
        $productFieldOptionDto = new ProductFieldOptionDto();
        if($productFieldOption->exists) {
            $productFieldOptionDto->id = $productFieldOption->id;
        }

        $productFieldOptionDto->name = $productFieldOption->getAttributeDto('name');
        $productFieldOptionDto->description = $productFieldOption->getAttributeDto('description');
        return $productFieldOptionDto;
    }

    public function saveDto(ProductField $productField, ProductFieldOptionDto $productFieldOptionDto): ProductFieldOption
    {
        $productFieldOption = $this->makeModel($productField, $productFieldOptionDto);
        $this->fillModel($productFieldOption, $productFieldOptionDto);
        $productFieldOption->save();
        return $productFieldOption;
    }

    public function makeModel(ProductField $productField, ProductFieldOptionDto $productFieldOptionDto): ProductFieldOption
    {
        if ($productFieldOptionDto->id) {
            $productField = $this->productFieldOptionRepository->getById($productFieldOptionDto->id);
            if($productField) {
                return $productField;
            }
        }

        $productFieldOption = $this->productFieldOptionRepository->getByMultilingualName($productField, $productFieldOptionDto->name);
        if($productFieldOption) {
            return $productFieldOption;
        }

        $productFieldOption = new ProductFieldOption();
        $productFieldOption->product_field_id = $productField->id;
        return $productFieldOption;
    }

    public function fillModel(ProductFieldOption $productFieldOption, ProductFieldOptionDto $productFieldOptionDto): void
    {
        $productFieldOption->setAttributeDto('name', $productFieldOptionDto->name);
        $productFieldOption->setAttributeDto('description', $productFieldOptionDto->description);
    }
}
