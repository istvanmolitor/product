<?php

namespace Molitor\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Molitor\Media\Models\MediaFile;
use Molitor\Media\Repositories\MediaFileRepositoryInterface;
use Molitor\Media\Repositories\MediaFolderRepositoryInterface;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()->get();

        $sourceImages = [
            base_path('packages/shop/resources/assets/product/1.png'),
            base_path('packages/shop/resources/assets/product/2.png'),
            base_path('packages/shop/resources/assets/product/3.png'),
        ];

        $sourceImages = array_values(array_filter($sourceImages, static function ($p) {
            return is_file($p);
        }));

        if (empty($sourceImages)) {
            return;
        }

        /** @var MediaFileRepositoryInterface $mediaFileRepository */
        $mediaFileRepository = app(MediaFileRepositoryInterface::class);
        /** @var MediaFolderRepositoryInterface $mediaFolderRepository */
        $mediaFolderRepository = app(MediaFolderRepositoryInterface::class);

        $targetFolder = $mediaFolderRepository->getOrCreateByPath(['Termékképek', 'Teszt']);

        $mediaFilesBySourcePath = [];
        foreach ($sourceImages as $srcPath) {
            $filename = basename($srcPath);
            $mimeType = mime_content_type($srcPath) ?: 'application/octet-stream';
            $size = filesize($srcPath) ?: 0;

            $mediaFile = MediaFile::query()
                ->where('folder_id', $targetFolder->id)
                ->where('filename', $filename)
                ->where('mime_type', $mimeType)
                ->where('size', $size)
                ->first();

            if (! $mediaFile) {
                $uploadedFile = new UploadedFile(
                    $srcPath,
                    $filename,
                    $mimeType,
                    null,
                    true
                );
                $mediaFile = $mediaFileRepository->store($uploadedFile, $targetFolder->id);
            }

            $mediaFilesBySourcePath[$srcPath] = $mediaFile;
        }

        foreach ($products as $product) {
            if ($product->productImages()->exists()) {
                continue;
            }

            $mainIndex = array_rand($sourceImages);

            foreach ($sourceImages as $index => $srcPath) {
                $mediaFile = $mediaFilesBySourcePath[$srcPath];

                $img = new ProductImage;
                $img->product_id = $product->id;
                $img->is_main = ($index === $mainIndex);
                $img->image = null;
                $img->image_url = $mediaFileRepository->getDownloadUrl($mediaFile);
                $img->sort = $index + 1;

                $title = ($product->name ?? 'Termék').' kép '.($index + 1);
                $img->setAttributeTranslation('title', $title, 'hu');
                $img->setAttributeTranslation('alt', $title, 'hu');

                $img->save();
            }
        }
    }
}
