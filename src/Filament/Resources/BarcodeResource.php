<?php

namespace Molitor\Product\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Molitor\Product\Filament\Resources\BarcodeResource\Pages;
use Molitor\Product\Models\Barcode;

class BarcodeResource extends Resource
{
    protected static ?string $model = Barcode::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-qr-code';

    public static function getNavigationGroup(): string
    {
        return __('product::common.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('product::barcode.title');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'product');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('barcode')
                ->label(__('product::common.barcode'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\Select::make('product_id')
                ->label(__('product::common.product'))
                ->relationship('product', 'sku')
                ->searchable()
                ->preload()
                ->required(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barcode')
                    ->label(__('product::common.barcode'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.sku')
                    ->label(__('product::common.product'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarcodes::route('/'),
            'create' => Pages\CreateBarcode::route('/create'),
            'edit' => Pages\EditBarcode::route('/{record}/edit'),
        ];
    }
}
