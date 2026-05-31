<?php

namespace Molitor\Product\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateProductUnitRequest',
    title: 'Update Product Unit Request',
    description: 'Data for updating a product unit',
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
class UpdateProductUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('acl', 'product_unit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productUnitId = $this->route('product_unit')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('product_units', 'code')->ignore($productUnitId),
            ],
            'enabled' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.short_name' => 'nullable|string|max:50',
        ];
    }
}
