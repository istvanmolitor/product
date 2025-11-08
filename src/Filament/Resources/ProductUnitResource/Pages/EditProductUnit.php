<?php

namespace Molitor\Product\Filament\Resources\ProductUnitResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Molitor\Product\Filament\Resources\ProductUnitResource;

class EditProductUnit extends EditRecord
{
    protected static string $resource = ProductUnitResource::class;

    public function getTitle(): string
    {
        return __('product::product_unit.edit');
    }

    public function getBreadcrumb(): string
    {
        return __('product::common.edit');
    }
}
