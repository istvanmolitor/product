<?php

namespace Molitor\Product\Filament\Resources\ProductCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Molitor\Product\Filament\Resources\ProductCategoryResource;

class EditProductCategory extends EditRecord
{
    protected static string $resource = ProductCategoryResource::class;

    public function getTitle(): string
    {
        return __('product::product_category.edit');
    }

    public function getBreadcrumb(): string
    {
        return __('product::common.edit');
    }
}
