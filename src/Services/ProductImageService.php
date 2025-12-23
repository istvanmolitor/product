<?php

namespace Molitor\Product\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductImage;
use Molitor\Product\Jobs\DownloadProductImageJob;
use Molitor\Product\Repositories\ProductImageRepositoryInterface;

class ProductImageService
{
    const DIRECTORY = 'product-images';

    public function __construct(
        private ProductImageRepositoryInterface $productImageRepository,
    )
    {

    }

    public function addProductImage(Product $product, string $url): ProductImage
    {
        $productImage = $this->productImageRepository->getImageByUrl($product, $url);
        if($productImage) {
            return $productImage;
        }

        $productImage = new ProductImage();
        $productImage->product_id = $product->id;
        $productImage->image_url = $url;
        $productImage->save();

        $this->queueDownload($productImage);
        return $productImage;
    }

    public function hasImageUrl(ProductImage $image): bool
    {
        return empty($image->url);
    }

    protected function isValidResponse($response): bool
    {
        $contentType = $response->header('Content-Type', '');
        $validTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        return in_array($contentType, $validTypes);
    }

    public function getExtByResource($response): string
    {
        $contentType = $response->header('Content-Type', '');
        $ext = match ($contentType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => null,
        };

        if($ext === null) {

        }


        return $ext;
    }

    public function downloadProductImage(ProductImage $productImage): bool
    {
        if(!$this->hasImageUrl($productImage)) {
            return false;
        }

        $response = Http::timeout(20)
            ->withHeaders(['Accept' => '*/*'])
            ->get($productImage->image_url)
            ->throw();

        if(!$this->isValidResponse($response)) {
            return false;
        }

        $content = $response->body();
        $maxBytes = 2 * 1024 * 1024; // 2MB
        if (strlen($content) > $maxBytes) {
            throw new RequestException($response);
        }

        $ext = $this->getExtByResource($response);

        $path = self::DIRECTORY . '/' . time() . '_' . Str::random(40) . '.' . $ext;

        Storage::disk('public')->put($path, $content, ['visibility' => 'public']);

        $productImage->image = $path;
        $productImage->image_url = null;
        return $productImage->save();
    }

    public function queueDownload(ProductImage $productImage): void
    {
        if($productImage->image_url === null) {
            return;
        }
        DownloadProductImageJob::dispatch($productImage->id);
    }
}
