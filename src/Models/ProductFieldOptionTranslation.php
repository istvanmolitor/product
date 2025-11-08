<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Molitor\Language\Models\TranslationModel;

class ProductFieldOptionTranslation extends TranslationModel
{
    public function getTranslatableModelClass(): string
    {
        return ProductFieldOption::class;
    }

    public function getTranslationForeignKey(): string
    {
        return 'product_field_option_id';
    }

    public function getTranslatableFields(): array
    {
        return [
            'name',
            'description'
        ];
    }
}
