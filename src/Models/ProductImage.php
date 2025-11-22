<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Molitor\Language\Models\TranslatableModel;

class ProductImage extends TranslatableModel
{
    protected $fillable = [
        'product_id',
        'is_main',
        'image_url',
        'image',
        'sort',
    ];

    public function getTranslationModelClass(): string
    {
        return ProductImageTranslation::class;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSrc(): string|null
    {
        return $this->image ? asset('storage/' . ltrim($this->image, '/')) : null;
    }
}
