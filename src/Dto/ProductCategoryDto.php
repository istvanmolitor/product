<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;
use Molitor\Language\Dto\TranslatableDto;

class ProductCategoryDto
{
    use TranslatableDto;

    public ?int $id = null;

    public ?string $source = null;

    public ProductCategoryPathDto $path;

    public Multilingual $description;

    public ?ImageDto $image = null;

    public function __construct()
    {
        $this->path = new ProductCategoryPathDto;
        $this->description = new Multilingual;
    }
}
