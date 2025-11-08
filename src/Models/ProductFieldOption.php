<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Molitor\Language\Models\TranslatableModel;

class ProductFieldOption extends TranslatableModel
{
    protected $fillable = [
        'product_field_id',
    ];

    public function productField(): BelongsTo
    {
        return $this->belongsTo(ProductField::class, 'product_field_id');
    }

    public function __toString(): string
    {
        return $this->productField . ': ' . $this->name;
    }

    public function getTranslationModelClass(): string
    {
        return ProductFieldOptionTranslation::class;
    }
}
