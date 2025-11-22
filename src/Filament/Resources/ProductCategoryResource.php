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
use Molitor\Product\Filament\Resources\ProductCategoryResource\Pages;
use Molitor\Product\Models\ProductCategory;
use Molitor\Product\Repositories\ProductCategoryRepositoryInterface;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-rectangle-group';
    public static function getNavigationGroup(): string
    {
        return __('product::common.group');
    }
    public static function getNavigationLabel(): string
    {
        return __('product::product_category.title');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'product');
    }

    public static function form(Schema $schema): Schema
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = app(LanguageRepositoryInterface::class);
        /** @var ProductCategoryRepositoryInterface $categoryRepository */
        $categoryRepository = app(ProductCategoryRepositoryInterface::class);

        return $schema->components([
            Forms\Components\Select::make('parent_id')
                ->label(__('product::common.parent_category'))
                ->options(
                    $categoryRepository->getAllWithRoot()->pluck('name', 'id')
                )
                ->default(0)
                ->required(),
            Forms\Components\TextInput::make('slug')
                ->label(__('product::common.slug'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\FileUpload::make('image')
                ->label(__('product::common.product_category_image'))
                ->image()
                ->disk('public')
                ->directory('product-categories')
                ->visibility('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(2048)
                ->nullable()
                ->preserveFilenames(false)
                ->getUploadedFileNameForStorageUsing(fn (\Illuminate\Http\UploadedFile $file): string => time() . '_' . $file->hashName()),
            TranslatableFields::schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('product::common.name'))
                    ->required()
                    ->maxLength(255),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('product::common.id'))
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('product::common.image'))
                    ->size(100)
                    ->disk('public')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('product::common.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('product::common.parent'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('product::common.slug'))
                    ->searchable()
                    ->toggleable(),
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
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}
