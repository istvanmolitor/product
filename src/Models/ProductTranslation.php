<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Molitor\Language\Models\TranslationModel;

class ProductTranslation extends TranslationModel
{
    public function getTranslatableModelClass(): string
    {
        return Product::class;
    }

    public function getTranslationForeignKey(): string
    {
        return 'product_id';
    }

    public function getTranslatableFields(): array
    {
        return [
            'name',
            'description',
            'short_description',
        ];
    }
}
