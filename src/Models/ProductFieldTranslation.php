<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Molitor\Language\Models\TranslationModel;

class ProductFieldTranslation extends TranslationModel
{
    public function getTranslatableModelClass(): string
    {
        return ProductField::class;
    }

    public function getTranslationForeignKey(): string
    {
        return 'product_field_id';
    }

    public function getTranslatableFields(): array
    {
        return [
            'name',
            'description',
        ];
    }
}
