<?php

namespace Molitor\Product\Filament\Resources\ProductUnitResource\Pages;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Molitor\Product\Filament\Resources\ProductUnitResource;

class ListProductUnits extends ListRecords
{
    protected static string $resource = ProductUnitResource::class;

    public function getTitle(): string
    {
        return __('product::product_unit.title');
    }

    public function getBreadcrumb(): string
    {
        return __('product::common.list');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('product::product_unit.create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    public function table(Table $table): Table
    {
        return ProductUnitResource::table($table)
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
