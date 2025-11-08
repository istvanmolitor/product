<?php

namespace Molitor\Product\Services;

use Illuminate\Database\Eloquent\Model;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Product\Dto\ProductFieldDto;
use Molitor\Product\Dto\ImageDto;
use Molitor\Product\Dto\ProductDto;
use Molitor\Product\Dto\ProductUnitDto;
use Molitor\Product\Models\ProductAttribute;
use Molitor\Product\Models\ProductTranslation;
use Molitor\Product\Repositories\ProductAttributeRepositoryInterface;
use Molitor\Product\Repositories\ProductUnitRepositoryInterface;

abstract class BaseProductManagerService
{
    public function __construct(
        protected ProductAttributeRepositoryInterface $productAttributeRepository
    )
    {
    }

    public function saveDto(ProductDto $productDto): void
    {
        $model = $this->getModelByDro($productDto);
        if(!$model) {
            $model = $this->getNewModel();
        }
        $this->saveProductModel($model, $productDto);
        $model->save();
    }


    abstract public function getModelByDro(ProductDto $productDto): Model|null;

    abstract protected function getNewModel(): Model;

    protected function isActive(ProductDto $productDto): bool
    {
        return (bool)$productDto->active;
    }

    protected function getCurrencyId(string|null $currencyCode): int
    {
        if(empty($currencyCode)) {
            return app(CurrencyRepositoryInterface::class)->getDefaultId();
        };
        return app(CurrencyRepositoryInterface::class)->getByCode($currencyCode)->id;
    }

    protected function getProductUnitId(ProductUnitDto $unit): int
    {
        $sortName = $unit->shortName->hu;
        if(empty($sortName)) {
            return app(ProductUnitRepositoryInterface::class)->getDefaultId();
        }
        return app(ProductUnitRepositoryInterface::class)->findBySortName($sortName)->id;
    }

    protected function addCategoryToModel(): void
    {

    }

    public function saveProductModel(Model $model, ProductDto $productDto): void
    {
        $model->fill([
            'active' => $this->isActive($productDto),
            'sku' => $productDto->sku,
            'slug' => $productDto->slug,
            'price' => $productDto->price,
            'currency_id' => $this->getCurrencyId($productDto->currency),
            'product_unit_id' => $this->getProductUnitId($productDto->productUnit),
        ]);
        $model->save();
        $model->setAttributeDto('name', $productDto->name);
        $model->setAttributeDto('description', $productDto->description);

        $this->saveModelAttributes($model, $productDto);
    }

    protected function attributeDtoToIds(ProductFieldDto $attributeDto): array
    {

    }

    abstract protected function saveModelAttributeIds(Model $model, array $attributeOptionIds): void;

    protected function saveModelAttributes(Model $model, ProductDto $productDto): void
    {
        $ids = [];
        /** @var ProductFieldDto $attributeDto */
        foreach ($productDto->getAttributes() as $attributeDto) {
            $ids = array_merge($ids, $this->attributeDtoToIds($attributeDto));
        }
        $this->saveModelAttributeIds($model, $ids);
    }

    /***********************************************************************/

    public function getDto(Model $product): ProductDTO
    {
        $productDto = new ProductDTO();
        $this->setDefaults($productDto, $product);
        $this->setTranslations($productDto, $product);
        $this->setImages($productDto, $product);
        $this->setUnit($productDto, $product);
        $this->setAttributes($productDto, $product);
        return $productDto;
    }

    protected function setDefaults(ProductDTO $productDto, Model $product): void
    {
        $productDto->price = $product->price;
        $productDto->currency = $product->currency;
    }

    protected function setTranslations(ProductDTO $productDto, Model $product): void
    {
        /** @var ProductTranslation $translation */
        foreach ($product->translations as $translation) {
            $code = $translation->getCode();
            $productDto->name->set($code, $translation->name);
            $productDto->description->set($code, $translation->description);
        }
    }

    protected function setUnit(ProductDTO $productDto, Model $product): void
    {
        foreach ($product->productUnit->translations as $translation) {
            $code = $translation->getCode();
            $productDto->productUnit->name->set($code, $translation->name);
            $productDto->productUnit->shortName->set($code, $translation->short_name);
        }
    }



    public function setImages(ProductDTO $productDto, Model $product): void
    {
        foreach ($product->productImages as $productImage) {
            $image = new ImageDto($productImage->url);
            $image->title->set('hu', $productImage->title);
            $image->alt->set('hu', $productImage->title);
            $productDto->addImage($image);
        }
    }

    public function setAttributes(ProductDTO $productDto, Model $product): void
    {
        /** @var ProductAttribute $productAttirbute */
        foreach ($product->productAttributes()->with('productFieldOption')->get() as $productAttirbute) {
            $attribute = new ProductFieldDto();

            $option = $productAttirbute->productFieldOption;
            foreach ($option->translations as $translation) {
                $attribute->value->set($translation->getCode(), $translation->name);
            }
            foreach ($option->productField->translations as $translation) {
                $attribute->name->set($translation->getCode(), $translation->name);
            }
            $productDto->addAttribute($attribute);
        }
    }
}
