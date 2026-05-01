<?php

namespace Molitor\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Molitor\Admin\Traits\HasAdminFilters;
use Molitor\Product\Http\Requests\StoreProductUnitRequest;
use Molitor\Product\Http\Requests\UpdateProductUnitRequest;
use Molitor\Product\Http\Resources\ProductUnitResource;
use Molitor\Product\Models\ProductUnit;
use OpenApi\Attributes as OA;

class ProductUnitController extends Controller
{
    use HasAdminFilters;

    #[OA\Get(
        path: '/api/admin/product/product-units',
        summary: 'List all product units',
        tags: ['Product Units'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ProductUnit')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer'),
                                new OA\Property(property: 'last_page', type: 'integer'),
                                new OA\Property(property: 'per_page', type: 'integer'),
                                new OA\Property(property: 'total', type: 'integer'),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = ProductUnit::with('translations');
        $productUnits = $this->applyAdminFilters($query, $request, ['code', 'name'])
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'data' => ProductUnitResource::collection($productUnits->items()),
            'meta' => [
                'current_page' => $productUnits->currentPage(),
                'last_page' => $productUnits->lastPage(),
                'per_page' => $productUnits->perPage(),
                'total' => $productUnits->total(),
            ],
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    #[OA\Get(
        path: '/api/admin/product/product-units/create',
        summary: 'Show form for creating a product unit',
        tags: ['Product Units'],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    public function create(): JsonResponse
    {
        return response()->json([]);
    }

    #[OA\Post(
        path: '/api/admin/product/product-units',
        summary: 'Store a new product unit',
        tags: ['Product Units'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreProductUnitRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductUnit'),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreProductUnitRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $productUnit = ProductUnit::create([
            'code' => $validated['code'],
            'enabled' => $validated['enabled'] ?? true,
        ]);

        if (isset($validated['translations'])) {
            foreach ($validated['translations'] as $languageId => $translation) {
                $productUnit->translations()->create([
                    'language_id' => $languageId,
                    'name' => $translation['name'] ?? '',
                    'short_name' => $translation['short_name'] ?? null,
                ]);
            }
        }

        $productUnit->load('translations');

        return response()->json([
            'data' => new ProductUnitResource($productUnit),
            'message' => __('product::product_unit.messages.created'),
        ], 201);
    }

    #[OA\Get(
        path: '/api/admin/product/product-units/{productUnit}',
        summary: 'Display a specific product unit',
        tags: ['Product Units'],
        parameters: [
            new OA\Parameter(name: 'productUnit', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductUnit'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(ProductUnit $productUnit): JsonResponse
    {
        $productUnit->load('translations');

        return response()->json([
            'data' => new ProductUnitResource($productUnit),
        ]);
    }

    #[OA\Get(
        path: '/api/admin/product/product-units/{productUnit}/edit',
        summary: 'Show form for editing a product unit',
        tags: ['Product Units'],
        parameters: [
            new OA\Parameter(name: 'productUnit', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function edit(ProductUnit $productUnit): JsonResponse
    {
        $productUnit->load('translations');

        return response()->json([
            'data' => new ProductUnitResource($productUnit),
        ]);
    }

    #[OA\Put(
        path: '/api/admin/product/product-units/{productUnit}',
        summary: 'Update a product unit',
        tags: ['Product Units'],
        parameters: [
            new OA\Parameter(name: 'productUnit', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProductUnitRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductUnit'),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function update(UpdateProductUnitRequest $request, ProductUnit $productUnit): JsonResponse
    {
        $validated = $request->validated();

        $productUnit->update([
            'code' => $validated['code'],
            'enabled' => $validated['enabled'] ?? $productUnit->enabled,
        ]);

        if (isset($validated['translations'])) {
            foreach ($validated['translations'] as $languageId => $translation) {
                $productUnit->translations()->updateOrCreate(
                    ['language_id' => $languageId],
                    [
                        'name' => $translation['name'] ?? '',
                        'short_name' => $translation['short_name'] ?? null,
                    ]
                );
            }
        }

        $productUnit->load('translations');

        return response()->json([
            'data' => new ProductUnitResource($productUnit),
            'message' => __('product::product_unit.messages.updated'),
        ]);
    }

    #[OA\Delete(
        path: '/api/admin/product/product-units/{productUnit}',
        summary: 'Delete a product unit',
        tags: ['Product Units'],
        parameters: [
            new OA\Parameter(name: 'productUnit', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(ProductUnit $productUnit): JsonResponse
    {
        $productUnit->delete();

        return response()->json([
            'message' => __('product::product_unit.messages.deleted'),
        ]);
    }
}
