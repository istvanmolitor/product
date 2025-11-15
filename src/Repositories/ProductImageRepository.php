<?php

namespace Molitor\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductImage;

class ProductImageRepository implements ProductImageRepositoryInterface
{
    private ProductImage $productImage;


    public function __construct()
    {
        $this->productImage = new ProductImage();
    }

    public function getUrlsByProduct(Product $product): array
    {
        return $this->productImage->where('product_id', $product->id)->orderBy('sort')->pluck('url')->toArray();
    }

    public function updateUrls(Product $product, array $urls): void
    {
        $oldUrls = $this->getUrlsByProduct($product);
        $insertUrls = array_diff($urls, $oldUrls);
        foreach ($insertUrls as $insertUrl) {
            $this->insertUrl($product, $insertUrl);
        }
        $this->deleteByUrls($product, array_diff($oldUrls, $urls));
    }

    public function getByUrls(Product $product, array $urls): Collection
    {
        return $this->productImage->where('product_id', $product->id)->whereIn('url', $urls)->orderBy('sort')->get();
    }

    public function delete(ProductImage $productImage): void
    {
        $productImage->delete();
    }

    public function deleteByUrls(Product $product, array $urls): void
    {
        /** @var ProductImage $productImage */
        foreach ($this->getByUrls($product, $urls) as $productImage) {
            $this->delete($productImage);
        }
    }

    public function getImageByUrl(Product $product, string $url)
    {
        return $this->productImage->where('product_id', $product->id)
            ->where('url', $url)->first();
    }

    public function getNextSort(Product $product): int
    {
        return 1 + (int)$this->productImage->where('product_id', $product->id)->max('sort');
    }

    public function insertUrl(Product $product, string $url, string $title = null): ProductImage
    {
        return $this->productImage->create(
            [
                'product_id' => $product->id,
                'url' => $url,
                'sort' => $this->getNextSort($product),
                'title' => $title,
                'file_id' => null,
            ]
        );
    }

    public function saveUrl(Product $product, string $url, string $title = null): ProductImage
    {
        $productImage = $this->getImageByUrl($product, $url);
        if (!$productImage) {
            return $this->insertUrl($product, $url, $title);
        }

        if (!empty($title)) {
            $productImage->title = $title;
            $productImage->save();
        }

        return $productImage;
    }

    public function addImageFile(Product $product): ProductImage
    {
        $productImage = new ProductImage();
        $productImage->product_id = $product->id;
        $productImage->url = null;
        $productImage->sort = 0;
        $productImage->title = '';

        $productImage->save();

        return $productImage;
    }

    public function saveImages(Product $product, array $urls): self
    {
        foreach ($urls as $url) {
            $this->saveUrl($product, $url);
        }
        return $this;
    }

    public function clearImages(Product $product): self
    {
        foreach ($product->productImages as $productImage) {
            $productImage->delete();
        }
        return $this;
    }
}
