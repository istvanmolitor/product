<?php

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\File\Models\ImageFile;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductImage;

interface ProductImageRepositoryInterface
{
    public function getUrlsByProduct(Product $product): array;

    public function updateUrls(Product $product, array $urls): void;

    public function getByUrls(Product $product, array $urls): Collection;

    public function delete(ProductImage $productImage): void;

    public function deleteByUrls(Product $product, array $urls): void;

    public function getImageByUrl(Product $product, string $url);

    public function getNextSort(Product $product): int;

    public function insertUrl(Product $product, string $url, string $title = null): ProductImage;

    public function saveUrl(Product $product, string $url, string $title = null): ProductImage;

    public function download(ProductImage $productImage): bool;

    public function addImageFile(Product $product, ImageFile $imageFile): ProductImage;

    public function saveImages(Product $product, array $urls): self;

    public function clearImages(Product $product): self;

    public function getSrc(?ProductImage $productImage): ?string;
}
