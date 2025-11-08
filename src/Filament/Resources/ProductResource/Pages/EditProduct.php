<?php

namespace Molitor\Product\Filament\Resources\ProductResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Molitor\Product\Filament\Resources\ProductResource;
use Molitor\Product\Models\ProductAttribute;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return __('product::product.edit');
    }

    public function getBreadcrumb(): string
    {
        return __('product::common.edit');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $attributes = ProductAttribute::query()
            ->where('product_id', $this->record->id)
            ->with('productFieldOption')
            ->get();

        $data['product_attributes_form'] = $attributes->map(function (ProductAttribute $value) {
            return [
                'product_field_id' => optional($value->productFieldOption)->product_field_id,
                'product_field_option_id' => $value->product_field_option_id,
                'sort' => $value->sort,
            ];
        })->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        ProductAttribute::query()->where('product_id', $this->record->id)->delete();

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
