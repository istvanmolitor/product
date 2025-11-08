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
use Molitor\Language\Filament\Components\TranslatableFields;
use Molitor\Language\Repositories\LanguageRepositoryInterface;
use Molitor\Product\Filament\Resources\ProductFieldOptionResource\Pages;
use Molitor\Product\Models\ProductFieldOption;
use Molitor\Product\Repositories\ProductFieldRepositoryInterface;

class ProductFieldOptionResource extends Resource
{
    protected static ?string $model = ProductFieldOption::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-list-bullet';
    public static function getNavigationGroup(): string
    {
        return __('product::common.group');
    }
    public static function getNavigationLabel(): string
    {
        return __('product::product_field_option.title');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'product_filed');
    }

    public static function form(Schema $schema): Schema
    {
        /** @var ProductFieldRepositoryInterface $productFieldRepository */
        $productFieldRepository = app(ProductFieldRepositoryInterface::class);

        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = app(LanguageRepositoryInterface::class);

        return $schema->components([
            Forms\Components\Select::make('product_field_id')
                ->label(__('product::common.product_field'))
                ->options($productFieldRepository->getOptions())
                ->searchable()
                ->preload()
                ->required(),
            TranslatableFields::schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('product::common.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('product::common.description'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(3),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('productField.name')
                    ->label(__('product::common.product_field')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('product::common.name')),
            ])
            ->filters([
            ])
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
            'index' => Pages\ListProductFieldOptions::route('/'),
            'create' => Pages\CreateProductFieldOption::route('/create'),
            'edit' => Pages\EditProductFieldOption::route('/{record}/edit'),
        ];
    }
}
