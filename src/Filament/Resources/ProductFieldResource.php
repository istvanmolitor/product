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
use Molitor\Product\Filament\Resources\ProductFieldResource\Pages;
use Molitor\Product\Models\ProductField;

class ProductFieldResource extends Resource
{
    protected static ?string $model = ProductField::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): string
    {
        return __('product::common.group');
    }
    public static function getNavigationLabel(): string
    {
        return __('product::product_field.title');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'product_filed');
    }

    public static function form(Schema $schema): Schema
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = app(LanguageRepositoryInterface::class);

        return $schema->components([
            Forms\Components\Toggle::make('multiple')
                ->label(__('product::common.multiple'))
                ->default(false),
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('product::common.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('multiple')
                    ->boolean()
                    ->label(__('product::common.multiple')),
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
            'index' => Pages\ListProductFields::route('/'),
            'create' => Pages\CreateProductField::route('/create'),
            'edit' => Pages\EditProductField::route('/{record}/edit'),
        ];
    }
}
