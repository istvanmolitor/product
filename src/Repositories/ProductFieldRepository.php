<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Language\Dto\Multilingual;
use Molitor\Product\Models\ProductField;

class ProductFieldRepository implements ProductFieldRepositoryInterface
{
    private ProductField $productField;
    private $cache = [];

    public function __construct()
    {
        $this->productField = new ProductField();
    }

    public function getByName(string $name, int|string|null $language): ProductField|null
    {
        return $this->productField->joinTranslation($language)->whereTranslation('name', $name)->first();
    }

    public function getByMultilingualName(Multilingual $name): ProductField|null
    {
        return $this->productField->whereMultilingual('name', $name)->first();
    }

    public function getAll(): Collection
    {
        return $this->productField->joinTranslation()->orderByTranslation('name')->get();
    }

    public function delete(ProductField $productField): void
    {
        $productField->delete();
    }

    public function getOptions(): array
    {
        return $this->productField->all()->pluck('name', 'id')->toArray();
    }

    public function getById(int $productFieldId): ProductField|null
    {
        return $this->productField->where('id', $productFieldId)->first();
    }

    public function create(string $name, int|string|null $language): ProductField
    {
        $productField = new ProductField();
        $productField->setAttributeTranslation('name', $name, $language);
        $productField->save();
        return $productField;
    }

    public function createByMultilingualName(Multilingual $name): ProductField
    {
        $productField = new ProductField();
        $productField->setAttributeDto('name', $name);
        $productField->save();
        return $productField;
    }
}
