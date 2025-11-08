<?php

namespace Molitor\Product\Filament\Resources\ProductFieldOptionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Molitor\Product\Filament\Resources\ProductFieldOptionResource;

class EditProductFieldOption extends EditRecord
{
    protected static string $resource = ProductFieldOptionResource::class;

    public function getTitle(): string
    {
        return __('product::product_field.edit');
    }

    public function getBreadcrumb(): string
    {
        return __('product::common.edit');
    }
}
