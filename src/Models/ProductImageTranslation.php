<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Molitor\Language\Models\TranslationModel;

class ProductImageTranslation extends TranslationModel
{
    public function getTranslatableModelClass(): string
    {
        return ProductImage::class;
    }

    public function getTranslationForeignKey(): string
    {
        return 'product_image_id';
    }

    public function getTranslatableFields(): array
    {
        return [
            'title',
            'alt',
        ];
    }
}
