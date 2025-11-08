<?php

namespace Molitor\Product\Filament\Resources\ProductFieldOptionResource\Pages;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Molitor\Product\Filament\Resources\ProductFieldOptionResource;

class ListProductFieldOptions extends ListRecords
{
    protected static string $resource = ProductFieldOptionResource::class;

    public function getBreadcrumb(): string
    {
        return __('product::common.list');
    }

    public function getTitle(): string
    {
        return __('product::product_field_option.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('product::product_field_option.create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    public function table(Table $table): Table
    {
        return ProductFieldOptionResource::table($table)
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
