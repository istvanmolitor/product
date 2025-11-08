<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Language\Dto\Multilingual;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Models\ProductFieldOption;

class ProductFieldOptionRepository implements ProductFieldOptionRepositoryInterface
{
    private ProductFieldOption $productFieldOption;
    private array $cache = [];

    public function __construct()
    {
        $this->productFieldOption = new ProductFieldOption();
    }

    public function getAll(): Collection
    {
        return $this->productFieldOption->orderBy('name')->get();
    }

    public function delete(ProductFieldOption $productFieldOption): void
    {
        $productFieldOption->delete();
    }

    public function getOptionsByProductFieldId(int $productFieldId): array
    {
        return $this->productFieldOption
            ->where('product_field_id', $productFieldId)
            ->get()->pluck('name', 'id')->toArray();
    }

    public function getById(int $id): ProductFieldOption|null
    {
        return $this->productFieldOption->where('id', $id)->first();
    }

    public function getByMultilingualName(ProductField $productField, Multilingual $name): ProductFieldOption|null
    {
        return $this->productFieldOption->where('product_field_id', $productField->id)->whereMultilingual('name', $name)->first();
    }

    public function create(ProductField $productField, string $name, int|string|null $language): ProductFieldOption
    {
        $productFieldOption = new ProductFieldOption();
        $productFieldOption->product_field_id = $productField->id;
        $productFieldOption->setAttributeTranslation('name', $name, $language);
        $productFieldOption->save();
        return $productFieldOption;
    }

    public function getOptionsByCustomerProductFieldId(int $param): array
    {
        return [];
    }
}
