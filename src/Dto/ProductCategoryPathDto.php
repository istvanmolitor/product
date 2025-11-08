<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;

class ProductCategoryPathDto
{
    public string $separator = '/';
    protected array $items = [];

    public function addProductCategory(Multilingual $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getLength(): int
    {
        return count($this->items);
    }

    public function get($language): string
    {
        return implode($this->separator, array_map(fn($productCategory) => $productCategory->get($language), $this->items));
    }

    public function __get($name): string
    {
        return $this->get($name);
    }

    public function getItem(int $index): Multilingual|null
    {
        return $this->items[$index] ?? null;
    }

    public function addItem(): Multilingual
    {
        $item = new Multilingual();
        $this->items[] = $item;
        return $item;
    }

    public function set(string $language, string $path): void
    {
        $exploded = explode($this->separator, $path);
        foreach ($exploded as $index => $item) {
            $productCategoryDto = $this->getItem($index);
            if(!$productCategoryDto) {
                $productCategoryDto = $this->addItem();
            }
            $productCategoryDto->set($language, $item);
        }
    }

    public function __set(string $language, string $path): void
    {
        $this->set($language, $path);
    }
}
