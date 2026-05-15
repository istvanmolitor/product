<?php

namespace Molitor\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductImage',
    title: 'Product image',
    description: 'Product image information',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'https://example.com/image.jpg'),
        new OA\Property(property: 'is_main', type: 'boolean', example: true),
        new OA\Property(property: 'sort', type: 'integer', example: 0),
    ]
)]
class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image_url,
            'is_main' => (bool) $this->is_main,
            'sort' => $this->sort,
        ];
    }
}
