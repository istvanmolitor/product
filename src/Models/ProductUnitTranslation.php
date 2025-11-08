<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Molitor\Language\Models\TranslationModel;

class ProductUnitTranslation extends TranslationModel
{
    public function getTranslatableModelClass(): string
    {
        return ProductUnit::class;
    }

    public function getTranslationForeignKey(): string
    {
        return 'product_unit_id';
    }

    public function getTranslatableFields(): array
    {
        return [
            'name',
            'short_name'
        ];
    }
}
