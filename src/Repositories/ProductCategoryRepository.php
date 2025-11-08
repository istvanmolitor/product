<?php

declare(strict_types=1);

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Product\Events\ProductCategoryDestroyEvent;
use Molitor\Product\Models\ProductCategory;

class ProductCategoryRepository implements ProductCategoryRepositoryInterface
{
    private ProductCategory $productCategory;

    public function __construct()
    {
        $this->productCategory = new ProductCategory();
    }

    public function refreshLeftRight(): void
    {
        $this->refreshLeftRightValue(0, 1);
    }

    private function refreshLeftRightValue(int $productCategoryId, int $leftValue): int
    {
        $subCategories = $this->productCategory->where('parent_id', $productCategoryId)->get();
        if ($subCategories->count() === 0) {
            if ($productCategoryId !== 0) {
                $this->productCategory->where('id', $productCategoryId)->update([
                    'left_value' => $leftValue,
                    'right_value' => $leftValue,
                ]);
            }
            return $leftValue;
        } else {
            foreach ($subCategories as $subCategory) {
                $subCategory->left_value = $leftValue;
                $rightValue = $this->refreshLeftRightValue($subCategory->id, $leftValue);
                $subCategory->right_value = $rightValue;
                $subCategory->save();

                $leftValue = $rightValue + 1;
            }
            return $rightValue;
        }
    }

    public function getRootProductCategories(): Collection
    {
        return $this->productCategory
            ->joinTranslation()
            ->where('product_categories.parent_id', 0)
            ->with('productCategories')
            ->orderByTranslation('name')
            ->select('product_categories.*')
            ->get();
    }

    public function getSubProductCategories(ProductCategory $productCategory): Collection
    {
        return $productCategory
            ->joinTranslation()
            ->productCategories()
            ->with('productCategories')
            ->orderByTranslation('name')
            ->select('product_categories.*')
            ->get();
    }

    public function getRootCategoryByName(string $name): ?ProductCategory
    {
        return $this->productCategory
            ->joinTranslation()
            ->where('product_categories.parent_id', 0)
            ->whereTranslation('name', $name)
            ->select('product_categories.*')
            ->first();
    }

    public function createRootProductCategory(string $name): ProductCategory
    {
        $productCategory = $this->getRootCategoryByName($name);
        if ($productCategory) {
            return $productCategory;
        }
        return $this->productCategory->create([
            'name' => $name,
        ]);
    }

    public function getSubCategoryByName(ProductCategory $parent, string $name): ?ProductCategory
    {
        return $this->productCategory
            ->joinTranslation()
            ->where('product_categories.parent_id', $parent->id)
            ->whereTranslation('name', $name)
            ->first();
    }

    public function createSubProductCategory(ProductCategory $parent, string $name): ProductCategory
    {
        $productCategory = $this->getSubCategoryByName($parent, $name);
        if ($productCategory) {
            return $productCategory;
        }
        return $this->productCategory->create([
            'parent_id' => $parent->id,
            'name' => $name,
        ]);
    }

    public function getPathCategories(ProductCategory $category): array
    {
        $path = $category->parent ? $this->getPathCategories($category->parent) : [];
        $path[] = $category;
        return $path;
    }

    private array $pathCache = [];

    public function getPath(ProductCategory $category): array
    {
        if (!array_key_exists($category->id, $this->pathCache)) {
            $path = [];
            foreach ($this->getPathCategories($category) as $pathCategory) {
                $path[] = $pathCategory->name;
            }
            $this->pathCache[$category->id] = $path;
        }
        return $this->pathCache[$category->id];
    }

    public function getCategoryToString(ProductCategory $category, $separator = '/'): string
    {
        return implode($separator, $this->getPath($category));
    }

    public function createProductCategory(array $path): ?ProductCategory
    {
        $parent = null;
        foreach ($path as $name) {
            if ($parent === null) {
                $parent = $this->createRootProductCategory($name);
            } else {
                $parent = $this->createSubProductCategory($parent, $name);
            }
            if (!$parent) {
                return null;
            }
        }

        return $parent;
    }

    public function getByPath(array $path): ?ProductCategory
    {
        $count = count($path);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
            return $this->getRootCategoryByName($path[0]);
        } else {
            $parent = $this->getByPath(array_slice($path, 0, $count - 1));
            return $this->getSubCategoryByName($parent, $path[$count - 1]);
        }
    }

    private ?Collection $allProductCategories = null;

    public function getAll(): Collection
    {
        if ($this->allProductCategories === null) {
            $this->allProductCategories = $this->productCategory->get();
        }
        return $this->allProductCategories;
    }

    public function getAllWithRoot(): Collection
    {
        $categories = $this->getAll();
        $categories->prepend((object)[
            'id' => 0,
            'name' => 'Főkategória',
            'parent_id' => null,
        ]);
        return $categories;
    }

    public function delete(ProductCategory $productCategory): bool
    {
        event(new ProductCategoryDestroyEvent($productCategory));
        foreach ($this->getSubProductCategories($productCategory) as $subProductCategory) {
            $this->delete($subProductCategory);
        }
        (new ProductCategoryProductRepository())->deleteByProductCategory($productCategory);
        return $productCategory->delete();
    }

    public function getOptions(): array
    {
        return $this->getAll()->pluck('name', 'id')->toArray();
    }
}
