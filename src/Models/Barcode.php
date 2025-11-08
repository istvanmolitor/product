<?php

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barcode extends Model
{
    protected $fillable = [
        'product_id',
        'barcode',
    ];

    public function __toString(): string
    {
        return (string)$this->barcode;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
