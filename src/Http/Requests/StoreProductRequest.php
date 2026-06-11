<?php

namespace Molitor\Product\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreProductRequest',
    title: 'Store Product Request',
    description: 'Data for creating a product',
    required: ['sku'],
    properties: [
        new OA\Property(property: 'sku', type: 'string', example: 'PROD-001'),
        new OA\Property(property: 'slug', type: 'string', example: 'product-001', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 99.99),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'product_unit_id', type: 'integer', example: 1, nullable: true),
        new OA\Property(
            property: 'product_category_ids',
            type: 'array',
            items: new OA\Items(type: 'integer')
        ),
        new OA\Property(
            property: 'product_images',
            type: 'array',
            items: new OA\Items(type: 'object')
        ),
        new OA\Property(
            property: 'translations',
            type: 'object',
            example: ['1' => ['name' => 'Product Name', 'description' => 'Product Description']]
        ),
    ]
)]
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('acl', 'product');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:255|unique:products,sku',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'price' => 'nullable|numeric|min:0',
            'active' => 'boolean',
            'product_unit_id' => 'nullable|exists:product_units,id',
            'product_category_ids' => 'nullable|array',
            'product_category_ids.*' => 'integer|distinct|exists:product_categories,id',
            'product_images' => 'nullable|array',
            'product_images.*.image_url' => 'required|string|max:2048',
            'product_images.*.is_main' => 'nullable|boolean',
            'product_images.*.sort' => 'nullable|integer|min:0',
            'translations' => 'nullable|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ];
    }
}
