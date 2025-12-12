<?php

namespace Molitor\Product\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Molitor\Product\Models\ProductCategory;
use Molitor\Product\Repositories\ProductCategoryRepository;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require_once(__DIR__ . '/data/categories.php');

        foreach ($data as $categoryData) {
            $this->createCategoryRecursive($categoryData, 0);
        }

        // refresh nested set left/right values
        /** @var ProductCategoryRepository $repo */
        $repo = app(ProductCategoryRepository::class);
        $repo->refreshLeftRight();
    }

    private function createCategoryRecursive(array $data, int $parentId = 0): ProductCategory
    {
        $names = Arr::get($data, 'name', []);
        $children = Arr::get($data, 'children', []);

        $category = $this->findExisting($names, $parentId);
        if (!$category) {
            $category = new ProductCategory();
            $category->parent_id = $parentId;
            // generate slug from preferred locale, ensure global uniqueness
            $baseName = $this->pickBaseName($names);
            $category->slug = $this->makeUniqueSlug(Str::slug($baseName));
            foreach ($names as $locale => $name) {
                $category->setAttributeTranslation('name', (string)$name, (string)$locale);
            }
            $category->save();
        } else {
            // ensure all provided translations are up to date
            foreach ($names as $locale => $name) {
                $category->setAttributeTranslation('name', (string)$name, (string)$locale);
            }
            $category->save();
        }

        foreach ($children as $child) {
            $this->createCategoryRecursive($child, $category->id);
        }

        return $category;
    }

    private function findExisting(array $names, int $parentId): ?ProductCategory
    {
        // Try to find a category under the same parent with any of the provided localized names
        $query = ProductCategory::query()->where('parent_id', $parentId);
        $query->where(function ($q) use ($names) {
            foreach ($names as $name) {
                $q->orWhereHas('translations', function ($qt) use ($name) {
                    $qt->where('name', (string)$name);
                });
            }
        });
        return $query->first();
    }

    private function pickBaseName(array $names): string
    {
        // Prefer Hungarian, then English, then German, then the first available
        foreach (['hu', 'en', 'de'] as $loc) {
            if (!empty($names[$loc])) {
                return (string)$names[$loc];
            }
        }
        // fallback to any first value
        foreach ($names as $name) {
            if (!empty($name)) {
                return (string)$name;
            }
        }
        return 'category';
    }

    private function makeUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug ?: 'category';
        $original = $slug;
        $i = 1;
        while (ProductCategory::query()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
