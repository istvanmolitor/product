<?php

namespace Molitor\Product\Filament\Resources\ProductUnitResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Molitor\Product\Filament\Resources\ProductUnitResource;

class CreateProductUnit extends CreateRecord
{
    protected static string $resource = ProductUnitResource::class;

    public function getBreadcrumb(): string
    {
        return __('product::common.create');
    }
}
