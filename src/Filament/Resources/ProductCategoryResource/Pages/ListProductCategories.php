<?php

namespace Molitor\Product\Filament\Resources\ProductCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Molitor\Product\Filament\Resources\ProductCategoryResource;

class ListProductCategories extends ListRecords
{
    protected static string $resource = ProductCategoryResource::class;

    public function getBreadcrumb(): string
    {
        return __('product::common.list');
    }

    public function getTitle(): string
    {
        return __('product::product_category.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('product::product_category.create'))
                ->icon('heroicon-o-plus'),
        ];
    }
}
