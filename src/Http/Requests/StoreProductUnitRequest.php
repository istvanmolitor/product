<?php

namespace Molitor\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreProductUnitRequest',
    title: 'Store Product Unit Request',
    description: 'Data for creating a product unit',
    required: ['code'],
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'pcs'),
        new OA\Property(property: 'enabled', type: 'boolean', example: true),
        new OA\Property(
            property: 'translations',
            type: 'object',
            example: ['1' => ['name' => 'Piece', 'short_name' => 'pc']]
        ),
    ]
)]
class StoreProductUnitRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:product_units,code',
            'enabled' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.short_name' => 'nullable|string|max:50',
        ];
    }
}

