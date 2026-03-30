<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;
use Molitor\Language\Dto\TranslatableDto;

class ProductDto
{
    use TranslatableDto;

    public ?int $id = null;

    public ?string $source = null;

    public ?bool $active = null;

    public ?string $sku = null;

    public Multilingual $name;

    public Multilingual $description;

    protected array $images = [];

    protected array $attributes = [];

    protected array $categories = [];

    public ?float $price = null;

    public ?string $currency = null;

    public ?string $slug = null;

    public ?string $url = null;

    public ?float $stock = null;

    public ProductUnitDto $productUnit;

    public ?float $weight = null;

    public function __construct()
    {
        $this->name = new Multilingual;
        $this->description = new Multilingual;
        $this->productUnit = new ProductUnitDto;
    }

    public function toArray(): array
    {
        $images = [];
        foreach ($this->images as $image) {
            $images[] = $image->toArray();
        }

        $attributes = [];
        foreach ($this->attributes as $attribute) {
            $attributes[] = $attribute->toArray();
        }

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name->toArray(),
            'description' => $this->description->toArray(),
            'images' => $images,
            'attributes' => $attributes,
            'categories' => $this->categories,
            'price' => $this->price,
            'currency' => $this->currency,
            'unit' => $this->productUnit?->toArray(),
        ];
    }

    public function addImage(ImageDto $image): self
    {
        $this->images[] = $image;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function addAttribute(ProductAttributeDto $attribute): self
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addCategory(ProductCategoryDto $category): self
    {
        $this->categories[] = $category;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}
