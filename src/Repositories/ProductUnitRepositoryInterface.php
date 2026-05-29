<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Language\Dto\Multilingual;
use Molitor\Product\Models\ProductUnit;

interface ProductUnitRepositoryInterface
{
    public function getAll(): Collection;

    public function getByCode(string $code): ?ProductUnit;

    public function getByShortName(string $shortName): ?ProductUnit;

    public function findOrCreate(string $shortName): ProductUnit;

    public function getDefault(): ?ProductUnit;

    public function getDefaultId(): ?int;

    public function getOptions(): array;

    public function getById(?int $id): ?ProductUnit;

    public function getByMultilingualSortName(Multilingual $shortName): ?ProductUnit;

    public function create(string $code, bool $enabled): ProductUnit;
}
