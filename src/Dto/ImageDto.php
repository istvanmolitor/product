<?php

namespace Molitor\Product\Dto;

use Molitor\Language\Dto\Multilingual;
use Molitor\Language\Dto\TranslatableDto;

class ImageDto
{
    use TranslatableDto;

    public string $url = '';
    public Multilingual $alt;
    public Multilingual $title;

    public function __construct()
    {
        $this->alt = new Multilingual();
        $this->title = new Multilingual();
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'alt' => $this->alt->toArray(),
            'title' => $this->title->toArray(),
        ];
    }
}
