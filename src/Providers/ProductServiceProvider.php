<?php

namespace Molitor\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Molitor\Product\Console\Commands\ProductDeleteImages;
use Molitor\Currency\Events\DefaultCurrencyChanged;
use Molitor\Product\Listeners\DefaultCurrencyChangedListener;
use Molitor\Product\Models\ProductImage;
use Molitor\Product\Observers\ProductImageObserver;
use Molitor\Product\Repositories\ProductCategoryProductRepository;
use Molitor\Product\Repositories\ProductCategoryProductRepositoryInterface;
use Molitor\Product\Repositories\ProductCategoryRepository;
use Molitor\Product\Repositories\ProductCategoryRepositoryInterface;
use Molitor\Product\Repositories\ProductFieldOptionRepository;
use Molitor\Product\Repositories\ProductFieldOptionRepositoryInterface;
use Molitor\Product\Repositories\ProductFieldRepository;
use Molitor\Product\Repositories\ProductFieldRepositoryInterface;
use Molitor\Product\Repositories\ProductAttributeRepository;
use Molitor\Product\Repositories\ProductAttributeRepositoryInterface;
use Molitor\Product\Repositories\ProductImageRepository;
use Molitor\Product\Repositories\ProductImageRepositoryInterface;
use Molitor\Product\Repositories\ProductRepository;
use Molitor\Product\Repositories\ProductRepositoryInterface;
use Molitor\Product\Repositories\ProductUnitRepository;
use Molitor\Product\Repositories\ProductUnitRepositoryInterface;

class ProductServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'product');
        
        ProductImage::observe(ProductImageObserver::class);

        Event::listen(
            DefaultCurrencyChanged::class,
            DefaultCurrencyChangedListener::class
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                ProductDeleteImages::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ProductFieldOptionRepositoryInterface::class, ProductFieldOptionRepository::class);
        $this->app->bind(ProductFieldRepositoryInterface::class, ProductFieldRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductImageRepositoryInterface::class, ProductImageRepository::class);
        $this->app->bind(ProductAttributeRepositoryInterface::class, ProductAttributeRepository::class);
        $this->app->bind(ProductCategoryProductRepositoryInterface::class, ProductCategoryProductRepository::class);
        $this->app->bind(ProductUnitRepositoryInterface::class, ProductUnitRepository::class);
    }
}
