<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Language\Dto\Multilingual;
use Molitor\Product\Models\ProductUnit;

interface ProductUnitRepositoryInterface
{
    public function getAll(): Collection;

    public function getByCode(string $code): ProductUnit|null;

    public function getByShortName(string $shortName): ProductUnit|null;

    public function findOrCreate(string $shortName): ProductUnit;

    public function getDefault(): ProductUnit|null;

    public function getDefaultId(): int|null;

    public function getOptions(): array;

    public function getById(int|null $id): ProductUnit|null;

    public function getByMultilingualSortName(Multilingual $shortName): ProductUnit|null;
}
