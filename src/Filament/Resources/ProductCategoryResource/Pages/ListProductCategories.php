<?php

namespace Molitor\Product\Filament\Resources\ProductCategoryResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Molitor\Product\Filament\Pages\ProductCategoriesPage;
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
            Action::make('tree_view')
                ->label('Fa nézet')
                ->icon('heroicon-o-rectangle-group')
                ->url(route('filament.admin.pages.product-categories-page'))
                ->color('gray'),
            CreateAction::make()
                ->label(__('product::product_category.create'))
                ->icon('heroicon-o-plus'),
        ];
    }
}
