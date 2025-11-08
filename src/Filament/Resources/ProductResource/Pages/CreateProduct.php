<?php

namespace Molitor\Product\Filament\Resources\ProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Molitor\Product\Filament\Resources\ProductResource;
use Molitor\Product\Models\ProductAttribute;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $rows = $this->data['product_attributes_form'] ?? [];
        if (!is_array($rows)) {
            return;
        }

        $seen = [];
        foreach ($rows as $row) {
            $optionId = $row['product_field_option_id'] ?? null;
            if (!empty($optionId) && !isset($seen[$optionId])) {
                $seen[$optionId] = true;
                ProductAttribute::create([
                    'product_id' => $this->record->id,
                    'product_field_option_id' => $optionId,
                    'sort' => $row['sort'] ?? 0,
                ]);
            }
        }
    }
}
