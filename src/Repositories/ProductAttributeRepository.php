<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductAttribute;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Models\ProductFieldOption;
use Illuminate\Database\Eloquent\Collection;

class ProductAttributeRepository implements ProductAttributeRepositoryInterface
{
    private ProductAttribute $productAttribute;

    public function __construct(
        private ProductFieldRepositoryInterface $productFieldRepository,
        private ProductFieldOptionRepositoryInterface $productFieldOptionRepository
    )
    {
        $this->productAttribute = new ProductAttribute();
    }

    public function setAttribute(Product $product, string $name, array|string $value, int|string|null $language = null): self
    {
        $field = $this->productFieldRepository->create($name, $language);

        if ($field->multiple && is_array($value)) {
            $this->deleteAttributesByProduct($product);
            foreach ($value as $valueElement) {
                $this->add($product, $this->productFieldOptionRepository->create($field, $valueElement, $language));
            }
        } else {
            $this->deleteAttributes($field)
                ->add($product, $this->productFieldOptionRepository->create($field, $value, $language));
        }
        return $this;
    }

    public function getProductAttributesByProduct(Product $product): Collection
    {
        return $this->productAttribute->with(['productField', 'productFieldOption'])->where('product_id', $product->id)->get();
    }

    public function deleteAttributesByProduct(Product $product): bool
    {
        $this->productAttribute
            ->where('product_id', $product->id)
            ->delete();
        return true;
    }

    public function delete(Product $product, ProductFieldOption $productFieldOption): self
    {
        $this->productAttribute
            ->where('product_id', $product->id)
            ->where('product_field_option_id', $productFieldOption->id)
            ->delete();

        return $this;
    }

    protected function exists(Product $product, ProductFieldOption $productFieldOption): bool
    {
        return $this->productAttribute
                ->where('product_id', $product->id)
                ->where('product_field_option_id', $productFieldOption->id)
                ->count() > 0;
    }

    protected function deleteAttributes(ProductField $productField): self
    {
        $this->productAttribute
            ->join(
                'product_field_options',
                'product_field_options.id',
                '=',
                'product_attributes.product_field_option_id'
            )
            ->where('product_id', $productField->id)
            ->delete();

        return $this;
    }

    private function add(Product $product, ProductFieldOption $productFieldOption): self
    {
        if ($this->exists($product, $productFieldOption)) {
            $this->productAttribute->create(
                [
                    'product_id' => $product->id,
                    'product_field_option_id' => $productFieldOption->id,
                    'sort' => 0,
                ]
            );
        }
        return $this;
    }

    public function save(Product $product, ProductFieldOption $productFieldOption, int $sort): ProductAttribute
    {
        if ($this->exists($product, $productFieldOption)) {
            $this->productAttribute->create(
                [
                    'product_id' => $product->id,
                    'product_field_option_id' => $productFieldOption->id,
                    'sort' => $sort,
                ]
            );
        }
        else {
            $this->productAttribute
                ->where('product_id', $product->id)
                ->where('product_field_option_id', $productFieldOption)
                ->update(['sort' => $sort]);
        }
    }
}
