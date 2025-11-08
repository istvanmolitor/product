<?php

namespace Molitor\Product\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Molitor\Product\Models\Product;

class ProductStoreEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
