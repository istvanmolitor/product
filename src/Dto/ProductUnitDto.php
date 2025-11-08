<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;
use Molitor\Language\Dto\TranslatableDto;

class ProductUnitDto
{
    use TranslatableDto;

    public int|null $id = null;

    public Multilingual $name;
    public Multilingual $shortName;

    public function __construct()
    {
        $this->name = new Multilingual();
        $this->shortName = new Multilingual();
    }

    public function toArray(): array
    {
        return $this->name->toArray();
    }
}
