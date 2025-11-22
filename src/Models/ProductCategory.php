<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Molitor\Language\Models\TranslatableModel;
use Molitor\Product\database\factories\ProductCategoryFactory;

class ProductCategory extends TranslatableModel
{
    use HasFactory;

    protected static function newFactory(): ProductCategoryFactory
    {
        return ProductCategoryFactory::new();
    }

    public function getTranslationModelClass(): string
    {
        return ProductCategoryTranslation::class;
    }

    protected $fillable = [
        'parent_id',
        'left_value',
        'right_value',
        'image',
        'image_url',
        'slug',
    ];

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function scopeCategory($query, ProductCategory $category)
    {
        return $query->where('left_value', '>=', $category->left_value)
            ->where('right_value', '<=', $category->right_value);
    }
}
