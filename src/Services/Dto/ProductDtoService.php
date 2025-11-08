<?php

namespace Molitor\Product\Services\Dto;

use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Product\Dto\ProductAttributeDto;
use Molitor\Product\Dto\ProductDto;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductAttribute;
use Molitor\Product\Repositories\ProductRepositoryInterface;

class ProductDtoService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductUnitDtoService $productUnitDtoService,
        protected ProductAttributeDtoService $productAttributeDtoService,
        protected CurrencyRepositoryInterface $currencyRepository,
    )
    {
    }

    public function makeDto(Product $product): ProductDto
    {
        $productDto = new ProductDto();
        $productDto->active = $product->active;
        $productDto->sku = $product->sku;
        $productDto->name = $product->getAttributeDto('name');
        $productDto->description = $product->getAttributeDto('description');
        $productDto->price = $product->price;
        $productDto->slug = $product->slug;
        $productDto->productUnit = $this->productUnitDtoService->makeDto($product->productUnit);

        /** @var ProductAttribute $attribute */
        foreach ($product->productAttributes as $attribute) {
            $productDto->addAttribute($this->productAttributeDtoService->makeDto($attribute));
        }

        return $productDto;
    }

    public function saveDto(ProductDto $productDto): Product
    {
        $product = $this->makeModel($productDto);
        $this->fillModel($product, $productDto);
        $product->save();

        $this->productAttributeDtoService->updateProductAttributes($product);

        return $product;
    }

    public function makeModel(ProductDto $productDto): Product
    {
        if($productDto->source === 'product' and $productDto->id)
        {
            $product = $this->productRepository->getById($productDto->id);
            if($product) {
                return $product;
            }
        }
        $product = $this->productRepository->getBySku($productDto->sku);
        if($product) {
            return $product;
        }
        $product = new Product();
        $product->sku = $productDto->sku;
        return $product;
    }

    public function fillModel(Product $product, ProductDto $productDto): void
    {
        $product->active = (bool)$productDto->active;
        $product->sku = $productDto->sku;
        $product->setAttributeDto('name', $productDto->name);
        $product->setAttributeDto('description', $productDto->description);
        $product->price = $productDto->price;
        $product->slug = $productDto->slug;
        $product->product_unit_id = $this->productUnitDtoService->saveDto($productDto->productUnit)->id;
        $product->currency_id = $this->currencyRepository->getByCode($productDto->currency)?->id;
    }
}
