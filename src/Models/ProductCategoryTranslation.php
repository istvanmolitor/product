<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Molitor\Language\Models\TranslationModel;

class ProductCategoryTranslation extends TranslationModel
{
    public $timestamps = false;

    public function getTranslatableModelClass(): string
    {
        return ProductCategory::class;
    }

    public function getTranslationForeignKey(): string
    {
        return 'product_category_id';
    }

    public function getTranslatableFields(): array
    {
        return [
            'name',
            'description',
        ];
    }
}
