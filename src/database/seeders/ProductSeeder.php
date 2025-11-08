<?php

namespace Molitor\Product\database\seeders;

use Illuminate\Database\Seeder;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductField;
use Molitor\Product\Models\ProductFieldOption;
use Molitor\Product\Models\ProductUnit;
use Molitor\Product\Models\ProductUnitTranslation;
use Molitor\User\Exceptions\PermissionException;
use Molitor\User\Services\AclManagementService;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

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

        if(app()->isLocal()) {
            $data = require_once(__DIR__ . '/data/product_fields.php');

            foreach ($data as $productFieldData) {
                $productField = new ProductField();
                foreach ($productFieldData['name'] as $locale => $name) {
                    $productField->setAttributeTranslation('name', $name, $locale);
                }
                $productField->save();
                foreach ($productFieldData['options'] as $option) {
                    $productFieldOption = new ProductFieldOption();
                    $productFieldOption->product_field_id = $productField->id;
                    foreach ($option['name'] as $locale => $name) {
                        $productFieldOption->setAttributeTranslation('name', $name, $locale);
                    }
                    $productFieldOption->save();
                }
            }

            Product::factory(10)->create();
        }
    }
}
