<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;

class ProductCategoryPathDto
{
    public string $separator = '/';
    protected array $items = [];

    public function addProductCategory(Multilingual $item): static
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

    public function setLength(int $length): bool
    {
        if($length < 1) {
            return false;
        }

        $oldLength = $this->getLength();
        if($length === $oldLength) {
            return false;
        }
        if($length > $oldLength) {
            for($i = $this->getLength(); $i < $length; $i++) {
                $this->addItem();
            }
        }
        else {
            $this->items = array_slice($this->items, 0, $length);
        }
        return true;
    }

    public function setArrayPath(string $language, array $path): bool
    {
        $length = count($path);
        if($length === 0) {
            return false;
        }

        if($length > $this->getLength()) {
            $this->setLength($length);
        }
        foreach ($path as $index => $item) {
            $this->items[$index]->set($language, $item);
        }
        return true;
    }

    public function getArrayPath(string $language): array
    {
        $path = [];
        foreach ($this->items as $item) {
            $path[] = $item->get($language);
        }
        return $path;
    }

    public function get($language): string
    {
        return implode($this->separator, $this->getArrayPath($language));
    }

    public function __get($language): string
    {
        return $this->get($language);
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

    public function setPath(string $language, string $path): void
    {
        $this->setArrayPath($language, explode($this->separator, $path));
    }

    public function __set(string $language, string $path): void
    {
        $this->setPath($language, $path);
    }
}
