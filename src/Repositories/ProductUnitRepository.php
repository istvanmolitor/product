<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Language\Dto\Multilingual;
use Molitor\Product\Models\ProductUnit;

class ProductUnitRepository implements ProductUnitRepositoryInterface
{
    private ProductUnit $productUnit;
    private array $shortNameCache = [];

    public function __construct()
    {
        $this->productUnit = new ProductUnit();
    }

    public function getByCode(string $code): ProductUnit|null
    {
        return $this->productUnit->where('code', $code)->first();
    }

    public function getAll(): Collection
    {
        return $this->productUnit
            ->joinTranslation()
            ->orderByTranslation('name')
            ->selectBase()
            ->get();
    }

    public function getByShortName(string $shortName): ProductUnit|null
    {
        if (!array_key_exists($shortName, $this->shortNameCache)) {
            $this->shortNameCache[$shortName] = $this->productUnit
                ->joinTranslation()
                ->whereTranslation('short_name', $shortName)
                ->selectBase()
                ->first();
        }
        return $this->shortNameCache[$shortName];
    }

    public function findOrCreate(string $shortName): ProductUnit
    {
        $productUnit = $this->getByShortName($shortName);
        if ($productUnit) {
            return $productUnit;
        }

        $this->shortNameCache[$shortName] = $this->productUnit->create([
            'name' => $shortName,
            'short_name' => $shortName,
        ]);
        return $this->shortNameCache[$shortName];
    }

    public function getDefault(): ProductUnit|null
    {
        return $this->productUnit->where('id', 1)->first();
    }

    public function getDefaultId(): int|null
    {
        return $this->getDefault()?->id;
    }

    public function getOptions(): array
    {
        return $this->productUnit->get()->pluck('name', 'id')->toArray();
    }

    public function getById(?int $id): ProductUnit|null
    {
        return $this->productUnit->where('id', $id)->first();
    }

    public function getByMultilingualSortName(Multilingual $shortName): ProductUnit|null
    {
        return $this->productUnit->whereMultilingual('short_name', $shortName)->first();
    }
}
