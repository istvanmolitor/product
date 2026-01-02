<?php

namespace Molitor\Product\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Gate;
use Molitor\Product\Filament\Resources\ProductCategoryResource;
use Molitor\Product\Repositories\ProductCategoryRepositoryInterface;

class ProductCategoriesPage extends Page
{
    protected string $view = 'product::filament.pages.product-categories';

    protected static ?string $slug = 'product-categories-page';

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static bool $shouldRegisterNavigation = true;

    public $categories = [];

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'product');
    }

    public static function getNavigationGroup(): string
    {
        return __('product::common.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('product::product_category.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('product::product_category.title');
    }

    public function mount(): void
    {
        /** @var ProductCategoryRepositoryInterface $categoryRepository */
        $categoryRepository = app(ProductCategoryRepositoryInterface::class);
        $categories = $categoryRepository->getRootProductCategories();

        $this->categories = $categories;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('list_view')
                ->label('Lista nézet')
                ->icon('heroicon-o-list-bullet')
                ->url(ProductCategoryResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}

