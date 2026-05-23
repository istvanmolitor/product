<?php

namespace Molitor\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductCategory;
use Molitor\Product\Models\ProductUnit;
use Molitor\User\Exceptions\PermissionException;
use Molitor\User\Services\AclManagementService;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        try {
            /** @var AclManagementService $aclService */
            $aclService = app(AclManagementService::class);
            $aclService->createPermission('product_unit', 'Termékek mennyiségi egységek kezelése', 'admin');
            $aclService->createPermission('product', 'Termékek kezelése', 'admin');
            $aclService->createPermission('product_filed', 'Termékek mezők kezelése', 'admin');
        } catch (PermissionException $e) {
            $this->command->error($e->getMessage());
        }

        $units = require_once __DIR__.'/data/product_units.php';

        foreach ($units as $code => $unitData) {
            $unit = new ProductUnit;
            $unit->code = $code;
            $unit->enabled = $unitData['enabled'];
            foreach ($unitData['name'] as $locale => $name) {
                $unit->setAttributeTranslation('name', $name, $locale);
            }
            foreach ($unitData['short_name'] as $locale => $name) {
                $unit->setAttributeTranslation('short_name', $name, $locale);
            }
            $unit->save();
        }

        $this->call(ProductCategorySeeder::class);

        if (! app()->isLocal()) {
            return;
        }

        $this->call(ProductFieldSeeder::class);

        $this->seedDemoProducts();

        $this->call(ProductAttributeSeeder::class);
        $this->call(ProductImageSeeder::class);
    }

    private function seedDemoProducts(): void
    {
        $huFaker = fake('hu_HU');
        $enFaker = fake('en_US');
        $unitIds = ProductUnit::query()->pluck('id')->all();
        $categoryIds = ProductCategory::query()->pluck('id')->all();

        if (empty($unitIds)) {
            return;
        }

        for ($index = 1; $index <= 10; $index++) {
            $huName = sprintf('Teszt termék %02d', $index);
            $enName = sprintf('Test product %02d', $index);
            $sku = sprintf('DEMO-%04d', $index);

            $product = Product::query()->create([
                'active' => true,
                'sku' => $sku,
                'slug' => Str::slug($enName).'-'.$index,
                'price' => $huFaker->randomFloat(2, 1000, 50000),
                'product_unit_id' => $huFaker->randomElement($unitIds),
            ]);

            $product->setAttributeTranslation('name', $huName, 'hu');
            $product->setAttributeTranslation('description', $huFaker->paragraph(2), 'hu');
            $product->setAttributeTranslation('name', $enName, 'en');
            $product->setAttributeTranslation('description', $enFaker->paragraph(2), 'en');
            $product->save();

            if (! empty($categoryIds)) {
                $product->productCategories()->syncWithoutDetaching([
                    $huFaker->randomElement($categoryIds),
                ]);
            }
        }
    }
}
