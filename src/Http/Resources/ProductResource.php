<?php

namespace Molitor\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Molitor\Language\Http\Resources\TranslationsResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Product',
    title: 'Product',
    description: 'Product information',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'sku', type: 'string', example: 'PROD-001'),
        new OA\Property(property: 'slug', type: 'string', example: 'product-001'),
        new OA\Property(property: 'name', type: 'string', example: 'Product Name'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 99.99),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'product_unit_id', type: 'integer', nullable: true),
        new OA\Property(
            property: 'product_images',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ProductImage')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class ProductResource extends JsonResource
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
            'sku' => $this->sku,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'active' => $this->active,
            'product_unit_id' => $this->product_unit_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'product_unit' => new ProductUnitSimpleResource($this->whenLoaded('productUnit')),
            'translations' => new TranslationsResource($this->getTranslations()),
            'product_images' => ProductImageResource::collection($this->whenLoaded('productImages')),
        ];
    }
}
