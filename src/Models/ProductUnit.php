<?php

declare(strict_types=1);

namespace Molitor\Product\Models;

use Molitor\Language\Models\TranslatableModel;

class ProductUnit extends TranslatableModel
{
    protected $fillable = [
        'enabled',
        'code',
    ];

    public function getTranslationModelClass(): string
    {
        return ProductUnitTranslation::class;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
