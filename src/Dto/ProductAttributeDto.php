<?php

namespace Molitor\Product\Dto;

class ProductAttributeDto
{
    public function __construct(
        public ProductFieldDto $field,
        public ProductFieldOptionDto $option,
        public int $sort = 0,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field->toArray(),
            'option' => $this->option->toArray(),
            'sort' => $this->sort,
        ];
    }
}
