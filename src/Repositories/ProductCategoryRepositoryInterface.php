<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Product\Models\ProductCategory;

interface ProductCategoryRepositoryInterface
{

    public function refreshLeftRight(): void;

    public function getRootProductCategories(): Collection;

    public function getSubProductCategories(ProductCategory $productCategory): Collection;

    public function getRootCategoryByName(string $name): ?ProductCategory;

    public function createRootProductCategory(string $name): ProductCategory;

    public function getSubCategoryByName(ProductCategory $parent, string $name): ?ProductCategory;

    public function createSubProductCategory(ProductCategory $parent, string $name): ProductCategory;

    public function getPathCategories(ProductCategory $category): array;

    public function getPath(ProductCategory $category): array;

    public function getCategoryToString(ProductCategory $category, $separator = '/'): string;

    public function createProductCategory(array $path): ?ProductCategory;

    public function getByPath(array $path): ?ProductCategory;

    public function getAll(): Collection;

    public function getAllWithRoot(): Collection;

    public function delete(ProductCategory $productCategory): bool;

    public function getOptions(): array;
}
