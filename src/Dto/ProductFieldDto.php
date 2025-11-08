<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;
use Molitor\Language\Dto\TranslatableDto;

class ProductFieldDto
{
    use TranslatableDto;

    public int|null $id = null;

    public Multilingual $name;

    public Multilingual $description;

    public bool $multiple = false;

    protected array $productFieldOptions = [];

    public function __construct()
    {
        $this->name = new Multilingual();
        $this->description = new Multilingual();
    }

    public function toArray(): array
    {
        if($this->multiple) {
            $value = [];
            foreach ($this->getProductFieldOptions() as $productFieldOption) {
                $value[] = $productFieldOption->toArray();
            }
        }
        else {
            $value = $this->getProductFieldOption()?->toArray();
        }

        return [
            'name' => $this->name->toArray(),
            'description' => $this->description->toArray(),
            'multiple' => $this->multiple,
            'value' => $value,
        ];
    }

    public function addProductFieldOption(ProductFieldOptionDto $productFieldOption): void
    {
        if(!$this->multiple) {
            $this->productFieldOptions = [];
        }
        $this->productFieldOptions[] = $productFieldOption;
    }

    public function getProductFieldOptions(): array
    {
        return $this->productFieldOptions;
    }

    public function getProductFieldOption(): ProductFieldOptionDto|null
    {
        if(count($this->productFieldOptions) === 0) {
            return null;
        }
        return $this->productFieldOptions[0];
    }
}
