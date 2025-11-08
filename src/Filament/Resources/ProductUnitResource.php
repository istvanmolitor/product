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
use Molitor\Language\Repositories\LanguageRepository;
use Molitor\Language\Repositories\LanguageRepositoryInterface;
use Molitor\Product\Filament\Resources\ProductUnitResource\Pages;
use Molitor\Product\Models\ProductUnit;
use Molitor\Product\Repositories\ProductUnitRepository;
use Molitor\Product\Repositories\ProductUnitRepositoryInterface;
use UnitEnum;

class ProductUnitResource extends Resource
{
    protected static ?string $model = ProductUnit::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-funnel';

    public static function getNavigationGroup(): string
    {
        return __('product::common.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('product::product_unit.title');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'product_unit');
    }

    public static function form(Schema $schema): Schema
    {
        /** @var LanguageRepository $languageRepository */
        $languageRepository = app(LanguageRepositoryInterface::class);

        return $schema->components([
            Forms\Components\Toggle::make('enabled')
                ->label(__('product::common.enabled'))
                ->default(true),
            Forms\Components\TextInput::make('code')
                ->label(__('product::common.code'))
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true),
            TranslatableFields::schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('product::common.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('short_name')
                    ->label(__('product::common.short_name'))
                    ->required()
                    ->maxLength(255),
            ])->columns(3),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean()
                    ->label(__('product::common.enabled')),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('product::common.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('translation.name')
                    ->label(__('product::common.name'))
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListProductUnits::route('/'),
            'create' => Pages\CreateProductUnit::route('/create'),
            'edit' => Pages\EditProductUnit::route('/{record}/edit'),
        ];
    }
}
