<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Molitor\Currency\Models\Currency;
use Molitor\Language\Models\TranslatableModel;
use Molitor\Product\database\factories\ProductFactory;

class Product extends TranslatableModel
{
    use SoftDeletes;
    use HasFactory;

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (empty($product->slug)) {
                $base = trim(($product->sku ?? '') . ' ' . ($product->name ?? ''));
                if ($base === '') {
                    $base = (string) now()->timestamp;
                }
                $product->slug = $product->generateUniqueSlug($base);
            }
        });
    }

    public function getTranslationModelClass(): string
    {
        return ProductTranslation::class;
    }

    protected $fillable = [
        'active',
        'sku',
        'slug',
        'price',
        'product_unit_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function __toString(): string
    {
        return $this->sku . ', ' . $this->name;
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')->orderBy('sort');
    }

    public function productImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->orderBy('sort');
    }

    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public function mainImage()
    {
        return $this->productImage()->where('is_main', true);
    }

    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'product_category_products',
            'product_id',
            'product_category_id'
        );
    }

    public function scopeCategoryFilter(Builder $builder, ProductCategory $productCategory = null)
    {
        if ($productCategory !== null) {
            $builder->join('product_category_products', 'product_category_products.product_id', '=', 'products.id')
                ->join(
                    'product_categories',
                    'product_categories.id',
                    '=',
                    'product_category_products.product_category_id'
                )
                ->where('product_categories.left_value', $productCategory->left_value)
                ->where('product_categories.right_value', $productCategory->right_value)
                ->select('products.*');
        }
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(Barcode::class, 'product_id');
    }

    protected function generateUniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = (string) now()->timestamp;
        }

        $original = $slug;
        $i = 2;
        while (static::withTrashed()->where('slug', $slug)->when($this->exists, function ($q) {
            $q->where('id', '!=', $this->id);
        })->exists()) {
            $slug = $original . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
