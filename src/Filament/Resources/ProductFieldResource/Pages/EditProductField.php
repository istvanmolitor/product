<?php

namespace Molitor\Product\Filament\Resources\ProductFieldResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Molitor\Product\Filament\Resources\ProductFieldResource;

class EditProductField extends EditRecord
{
    protected static string $resource = ProductFieldResource::class;

    public function getTitle(): string
    {
        return __('product::product_field.edit');
    }

    public function getBreadcrumb(): string
    {
        return __('product::common.edit');
    }
}

