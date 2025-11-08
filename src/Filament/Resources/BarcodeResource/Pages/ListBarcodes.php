<?php

namespace Molitor\Product\Filament\Resources\BarcodeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Molitor\Product\Filament\Resources\BarcodeResource;

class ListBarcodes extends ListRecords
{
    protected static string $resource = BarcodeResource::class;

    public function getBreadcrumb(): string
    {
        return __('product::common.list');
    }

    public function getTitle(): string
    {
        return __('product::barcode.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('product::barcode.create'))
                ->icon('heroicon-o-plus'),
        ];
    }
}

