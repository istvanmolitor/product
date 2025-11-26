<?php

declare(strict_types=1);

namespace Molitor\Product\Listeners;

use Illuminate\Support\Facades\Log;
use Molitor\Currency\Events\DefaultCurrencyChanged;
use Molitor\Currency\Services\Price;
use Molitor\Product\Models\Product;

class DefaultCurrencyChangedListener
{
    public function handle(DefaultCurrencyChanged $event): void
    {
        if ($event->previousCurrency === null || $event->previousCurrency->id === $event->currency->id) {
            return;
        }

        Product::query()
            ->select(['id', 'price'])
            ->whereNotNull('price')
            ->chunkById(200, function ($products) use ($event) {
                /** @var Product $product */
                foreach ($products as $product) {
                    $oldPriceService = new Price((float)$product->price, $event->previousCurrency);
                    $newPriceService = $oldPriceService->exchange($event->currency);
                    $newPrice = (float) $newPriceService->price;

                    if (!self::isNearlyEqual((float)$product->price, $newPrice)) {
                        $product->price = $newPrice;
                        $product->save();
                    }
                }
            });
    }

    protected static function isNearlyEqual(float $a, float $b, float $epsilon = 0.00001): bool
    {
        return abs($a - $b) < $epsilon;
    }
}
