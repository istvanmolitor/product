<?php

namespace Molitor\Product\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Molitor\Product\Models\ProductCategory;

class ProductCategoryDestroyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProductCategory $productCategory;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProductCategory $productCategory)
    {
        $this->productCategory = $productCategory;
    }
}
