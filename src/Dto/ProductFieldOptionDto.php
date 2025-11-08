<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;
use Molitor\Language\Dto\TranslatableDto;

class ProductFieldOptionDto
{
    use TranslatableDto;

    public int|null $id = null;

    public Multilingual $name;

    public Multilingual $description;

    public function __construct()
    {
        $this->name = new Multilingual();
        $this->description = new Multilingual();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->toArray(),
            'description' => $this->description->toArray(),
        ];
    }
}
