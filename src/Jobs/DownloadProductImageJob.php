<?php

namespace Molitor\Product\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Molitor\Product\Repositories\ProductImageRepositoryInterface;
use Molitor\Product\Services\ProductImageService;

class DownloadProductImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $productImageId;

    public function __construct(int $productImageId)
    {
        $this->productImageId = $productImageId;
    }

    public int $tries = 3;

    public int $timeout = 60;

    public function handle(ProductImageService $service): void
    {
        /** @var ProductImageRepositoryInterface $repository */
        $repository = app(ProductImageRepositoryInterface::class);
        $productImage = $repository->getById($this->productImageId);
        if($productImage) {
            $service->downloadProductImage($productImage);
        }
    }
}
