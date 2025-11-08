<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryProduct extends Model
{
    protected $fillable = [
        'product_id',
        'product_category_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
