<?php

namespace Molitor\Product\database\seeders;

use Illuminate\Database\Seeder;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Models\ProductFieldOption;
use Molitor\Product\Models\ProductUnit;
use Molitor\Product\Models\ProductCategory;
use Molitor\User\Exceptions\PermissionException;
use Molitor\User\Services\AclManagementService;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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

        $units = require_once(__DIR__ . '/data/product_units.php');

        foreach ($units as $code => $unitData) {
            $unit = new ProductUnit();
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

        // Seed product categories from data file (multi-language, multi-level)
        $this->call(ProductCategorySeeder::class);

        if(!app()->isLocal()) {
            return;
        }

        // Seed product fields and options in a dedicated seeder
        $this->call(ProductFieldSeeder::class);

        // In local environment we skip random category factories in favor of deterministic seeder above

        Product::factory(100)->create();

        // After products are created, randomly assign attributes and seed images
        $this->call(ProductAttributeSeeder::class);
        // Seed product images for the generated products
        $this->call(ProductImageSeeder::class);
    }
}
