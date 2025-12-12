<?php

namespace Molitor\Product\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For each existing product, attach local demo images as if uploaded via the admin UI
        $products = Product::query()->get();

        // Source demo images within the repo
        $sourceImages = [
            base_path('packages/shop/resources/assets/product/1.png'),
            base_path('packages/shop/resources/assets/product/2.png'),
            base_path('packages/shop/resources/assets/product/3.png'),
        ];

        // Keep only those that exist
        $sourceImages = array_values(array_filter($sourceImages, static function ($p) { return is_file($p); }));

        if (empty($sourceImages)) {
            return; // no demo images available
        }

        foreach ($products as $product) {
            // Skip if the product already has images
            if ($product->productImages()->exists()) {
                continue;
            }

            $destDir = 'products/' . $product->id . '/images';

            // Choose a random image to be the main image for this product
            $mainIndex = array_rand($sourceImages);

            foreach ($sourceImages as $index => $srcPath) {
                $filename = 'image-' . ($index + 1) . '.png';
                $destPath = $destDir . '/' . $filename;

                if (!Storage::disk('public')->exists($destPath)) {
                    $contents = @file_get_contents($srcPath);
                    if ($contents !== false) {
                        Storage::disk('public')->put($destPath, $contents);
                    }
                }

                $img = new ProductImage();
                $img->product_id = $product->id;
                $img->is_main = ($index === $mainIndex);
                $img->image = $destPath; // stored on public disk
                $img->image_url = null; // mimic uploaded file (not external URL)
                $img->sort = $index + 1;

                // Translatable fields
                $title = ($product->name ?? 'Termék') . ' kép ' . ($index + 1);
                $img->setAttributeTranslation('title', $title, 'hu');
                $img->setAttributeTranslation('alt', $title, 'hu');

                $img->save();
            }
        }
    }
}
