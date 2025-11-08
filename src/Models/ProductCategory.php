<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Molitor\Language\Models\TranslatableModel;

class ProductCategory extends TranslatableModel
{

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
}
