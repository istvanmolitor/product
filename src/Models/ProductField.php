<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Molitor\Language\Models\TranslatableModel;

class ProductField extends TranslatableModel
{
    protected $fillable = [
        'multiple',
    ];

    public function productFieldOptions(): HasMany
    {
        return $this->hasMany(ProductFieldOption::class, 'product_field_id');
    }

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function getTranslationModelClass(): string
    {
        return ProductFieldTranslation::class;
    }
}
