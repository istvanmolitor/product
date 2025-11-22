<?php

namespace Molitor\Product\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Molitor\Currency\Repositories\CurrencyRepository;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Language\Filament\Components\TranslatableFields;
use Molitor\Language\Repositories\LanguageRepository;
use Molitor\Language\Repositories\LanguageRepositoryInterface;
use Molitor\Product\Filament\Resources\ProductResource\Pages;
use Molitor\Product\Models\Product;
use Molitor\Product\Repositories\ProductCategoryRepository;
use Molitor\Product\Repositories\ProductCategoryRepositoryInterface;
use Molitor\Product\Repositories\ProductFieldOptionRepository;
use Molitor\Product\Repositories\ProductFieldOptionRepositoryInterface;
use Molitor\Product\Repositories\ProductFieldRepository;
use Molitor\Product\Repositories\ProductFieldRepositoryInterface;
use Molitor\Product\Repositories\ProductUnitRepository;
use Molitor\Product\Repositories\ProductUnitRepositoryInterface;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-tag';
    public static function getNavigationGroup(): string
    {
        return __('product::common.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('product::product.title');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'product');
    }

    public static function form(Schema $schema): Schema
    {
        /** @var LanguageRepository $languageRepository */
        $languageRepository = app(LanguageRepositoryInterface::class);
        /** @var ProductUnitRepository $productUnitRepository */
        $productUnitRepository = app(ProductUnitRepositoryInterface::class);
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = app(CurrencyRepositoryInterface::class);
        /** @var ProductCategoryRepository $productCategoryRepository */
        $productCategoryRepository = app(ProductCategoryRepositoryInterface::class);
        /** @var ProductFieldRepository $roductFieldRepository */
        $productFieldRepository = app(ProductFieldRepositoryInterface::class);
        /** @var ProductFieldOptionRepository $productFieldOptionRepository */
        $productFieldOptionRepository = app(ProductFieldOptionRepositoryInterface::class);

        return $schema->components([
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Alapadatok')
                        ->schema([
                            Forms\Components\Toggle::make('active')
                                ->label(__('product::common.active'))
                                ->default(true),
                            Forms\Components\TextInput::make('sku')
                                ->label(__('product::common.sku'))
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('slug')
                                ->label(__('product::common.slug'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            TranslatableFields::schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('product::common.name'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->label(__('product::common.description'))
                                    ->columnSpanFull(),
                            ]),
                            Forms\Components\Select::make('productCategories')
                                ->label(__('product::common.categories'))
                                ->relationship('productCategories', 'id')
                                ->multiple()
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('product_unit_id')
                                ->label(__('product::common.product_unit'))
                                ->options($productUnitRepository->getOptions())
                                ->default($productUnitRepository->getDefaultId())
                                ->searchable()
                                ->preload()
                                ->required(),
                            Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->label(__('product::common.price'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(99999999999)
                                        ->required(),
                                    Forms\Components\Select::make('currency_id')
                                        ->label(__('product::common.currency'))
                                        ->relationship('currency', 'code')
                                        ->default($currencyRepository->getDefaultId())
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                ]),
                        ]),
                    Tabs\Tab::make('Tulajdonságok')
                        ->schema([
                            Forms\Components\Repeater::make('product_attributes_form')
                                ->label(__('product::common.product_attributes'))
                                ->dehydrated(false)
                                ->orderColumn('sort')
                                ->default([])
                                ->schema([
                                    Forms\Components\Select::make('product_field_id')
                                        ->label(__('product::common.product_field'))
                                        ->options($productFieldRepository->getOptions())
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->required(),
                                    Forms\Components\Select::make('product_field_option_id')
                                        ->label(__('product::common.product_field_option'))
                                        ->options(function ($get) use ($productFieldOptionRepository) {
                                            $fieldId = $get('product_field_id');
                                            if (!$fieldId) {
                                                return [];
                                            }
                                            return $productFieldOptionRepository->getOptionsByProductFieldId((int)$fieldId);
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->disabled(fn($get) => empty($get('product_field_id'))),
                                ])->columns(2),
                        ]),
                    Tabs\Tab::make(__('product::common.product_images'))->schema([
                        Forms\Components\Repeater::make('productImages')
                            ->label(__('product::common.image_data'))
                            ->relationship('productImages')
                            ->orderColumn('sort')
                            ->reorderable()
                            ->schema([
                                Forms\Components\Toggle::make('is_main')
                                    ->label('Főkép')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state) {
                                            $productImages = $get('../../productImages') ?? [];
                                            foreach ($productImages as $index => $image) {
                                                if (array_key_exists('is_main', $image) && $image['is_main'] && $index != array_search($get(), $productImages)) {
                                                    $set("../../productImages.{$index}.is_main", false);
                                                }
                                            }
                                        }
                                    }),
                                Grid::make(3)->schema([
                                    Group::make([
                                        Forms\Components\FileUpload::make('image')
                                            ->label(__('product::common.image'))
                                            ->image()
                                            ->disk('public')
                                            ->directory('product-images')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(2048)
                                            ->nullable()
                                            ->preserveFilenames(false)
                                            ->getUploadedFileNameForStorageUsing(fn (\Illuminate\Http\UploadedFile $file): string => time() . '_' . $file->hashName()),
                                        Forms\Components\TextInput::make('url')
                                            ->label('Kép URL')
                                            ->url()
                                            ->nullable(),
                                    ])->columnSpan(1)->gap(1),
                                    Group::make([
                                        Forms\Components\Repeater::make('translations')
                                            ->default(fn () => [
                                                ['language_id' => $languageRepository->getDefaultId()],
                                            ])
                                            ->label(__('product::common.translations'))
                                            ->relationship('translations')
                                            ->schema([
                                                Forms\Components\Select::make('language_id')
                                                    ->label(__('product::common.language'))
                                                    ->relationship(name: 'language', titleAttribute: 'code')
                                                    ->default($languageRepository->getDefaultId())
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Cím')
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('alt')
                                                    ->label('Alt szöveg')
                                                    ->maxLength(255),
                                            ])->columns(2),
                                    ])->columnSpan(2)->gap(2),
                                ]),
                            ])->columns(1),
                        ]),
                    Tabs\Tab::make('Vonalkódok')->schema([
                        Forms\Components\Repeater::make('barcodes')
                            ->label(__('product::barcode.title'))
                            ->relationship('barcodes')
                            ->default([])
                            ->schema([
                                Forms\Components\TextInput::make('barcode')
                                    ->label(__('product::common.barcode'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ])->columns(1),
                    ]),
                ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->label(__('product::common.active')),
                Tables\Columns\ImageColumn::make('mainImage.image')
                    ->label(__('product::common.image'))
                    ->size(100)
                    ->disk('public')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('translation.name')
                    ->label(__('product::common.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('product::common.price'))
                    ->formatStateUsing(fn ($record) => $record->price . ' ' . $record->currency->code)
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
