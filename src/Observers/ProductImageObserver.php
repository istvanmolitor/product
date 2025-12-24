<?php

namespace Molitor\Product\Observers;

use Molitor\Product\Models\ProductImage;
use Molitor\Product\Services\ProductImageService;

class ProductImageObserver
{
    public function __construct(
        private ProductImageService $productImageService,
    )
    {
    }

    /**
     * Handle the ProductImage "created" event.
     */
    public function created(ProductImage $productImage): void
    {
        if ($productImage->image_url !== null) {
            $this->productImageService->queueDownload($productImage);
        }
    }

    /**
     * Handle the ProductImage "updated" event.
     */
    public function updated(ProductImage $productImage): void
    {
        // If image_url was changed and is not null, queue download
        if ($productImage->wasChanged('image_url') && $productImage->image_url !== null) {
            $this->productImageService->queueDownload($productImage);
        }
    }
}

