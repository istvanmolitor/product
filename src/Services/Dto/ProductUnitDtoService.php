<?php

namespace Molitor\Product\Services\Dto;

use Molitor\Product\Dto\ProductUnitDto;
use Molitor\Product\Models\ProductUnit;
use Molitor\Product\Repositories\ProductUnitRepositoryInterface;

class ProductUnitDtoService
{
    public function __construct(
        private ProductUnitRepositoryInterface $productUnitRepository,
    )
    {
    }

    public function makeDto(ProductUnit $productUnit): ProductUnitDto
    {
        $productUnitDto = new ProductUnitDto();
        $productUnitDto->name = $productUnit->getAttributeDto('name');
        $productUnitDto->shortName = $productUnit->getAttributeDto('short_name');
        return $productUnitDto;
    }

    public function saveDto(ProductUnitDto $productUnitDto): ProductUnit
    {
        $productUnit = $this->makeModel($productUnitDto);
        $this->fillModel($productUnit, $productUnitDto);
        $productUnit->save();
        return $productUnit;
    }

    public function makeModel(ProductUnitDto $productUnitDto): ProductUnit
    {
        if ($productUnitDto->id) {
            $productUnit = $this->productUnitRepository->getById($productUnitDto->id);
            if ($productUnit) {
                return $productUnit;
            }
        }
        $productUnit = $this->productUnitRepository->getByMultilingualSortName($productUnitDto->shortName);
        if ($productUnit) {
            return $productUnit;
        }

        return new ProductUnit();
    }

    public function fillModel(ProductUnit $productUnit, ProductUnitDto $productUnitDto): void
    {
        $productUnit->setAttributeDto('name', $productUnitDto->name);
        $productUnit->setAttributeDto('short_name', $productUnitDto->shortName);
    }
}
