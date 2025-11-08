<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Language\Dto\Multilingual;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Models\ProductFieldOption;

interface ProductFieldOptionRepositoryInterface
{
    public function getAll(): Collection;

    public function delete(ProductFieldOption $productFieldOption): void;

    public function getOptionsByProductFieldId(int $productFieldId): array;

    public function getById(int $id): ProductFieldOption|null;

    public function getByMultilingualName(ProductField $productField, Multilingual $name): ProductFieldOption|null;

    public function create(ProductField $productField, string $name, int|string|null $language): ProductFieldOption;

    public function getOptionsByCustomerProductFieldId(int $param): array;
}
