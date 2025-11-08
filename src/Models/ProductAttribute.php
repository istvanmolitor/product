<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_field_option_id',
        'sort',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productFieldOption(): BelongsTo
    {
        return $this->belongsTo(ProductFieldOption::class, 'product_field_option_id');
    }
}
