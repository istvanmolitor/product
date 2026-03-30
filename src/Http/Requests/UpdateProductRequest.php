<?php

namespace Molitor\Product\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateProductRequest',
    title: 'Update Product Request',
    description: 'Data for updating a product',
    required: ['sku'],
    properties: [
        new OA\Property(property: 'sku', type: 'string', example: 'PROD-001'),
        new OA\Property(property: 'slug', type: 'string', example: 'product-001', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 99.99),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'product_unit_id', type: 'integer', example: 1, nullable: true),
        new OA\Property(
            property: 'translations',
            type: 'object',
            example: ['1' => ['name' => 'Product Name', 'description' => 'Product Description']]
        ),
    ]
)]
class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'price' => 'nullable|numeric|min:0',
            'active' => 'boolean',
            'product_unit_id' => 'nullable|exists:product_units,id',
            'translations' => 'nullable|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ];
    }
}
