<?php

namespace Molitor\Product\Tests\Feature;

use Molitor\Product\Providers\ProductServiceProvider;
use Tests\TestCase;

class PackageSmokeTest extends TestCase
{
    public function test_service_provider_is_loaded(): void
    {
        $this->assertTrue(class_exists(ProductServiceProvider::class));
        $this->assertTrue($this->app->providerIsLoaded(ProductServiceProvider::class));
    }
}

