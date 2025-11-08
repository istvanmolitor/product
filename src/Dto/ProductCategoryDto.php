<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;
use Molitor\Language\Dto\TranslatableDto;

class ProductCategoryDto
{
    use TranslatableDto;

    public string $id = '';

    public ProductCategoryPathDto $path;

    public Multilingual $description;

    public function __construct()
    {
        $this->path = new ProductCategoryPathDto();
        $this->description = new Multilingual();
    }
}
