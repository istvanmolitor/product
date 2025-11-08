<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Language\Dto\Multilingual;
use Molitor\Product\Models\ProductField;

interface ProductFieldRepositoryInterface
{
    public function getById(int $productFieldId): ProductField|null;
    public function getByName(string $name, int|string|null $language): ProductField|null;

    public function getByMultilingualName(Multilingual $name): ProductField|null;

    public function create(string $name, int|string|null $language): ProductField;

    public function createByMultilingualName(Multilingual $name): ProductField;

    public function getAll(): Collection;

    public function delete(ProductField $productField): void;

    public function getOptions(): array;
}
