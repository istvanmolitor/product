<?php

namespace Molitor\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Molitor\Product\DataTables\ProductUnitDataTable;
use Molitor\Product\Http\Requests\StoreProductUnitRequest;
use Molitor\Product\Http\Requests\UpdateProductUnitRequest;
use Molitor\Product\Http\Resources\ProductUnitResource;
use Molitor\Product\Models\ProductUnit;
use Molitor\Product\Repositories\ProductUnitRepositoryInterface;
use OpenApi\Attributes as OA;

class ProductUnitController extends Controller
{
    public function __construct(
        private ProductUnitRepositoryInterface $productUnitRepository
    ) {}

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
    public function index(ProductUnitDataTable $dataTable): AnonymousResourceCollection
    {
        return $dataTable->getResponse();
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

        $productUnit = $this->productUnitRepository->create(
            $validated['code'],
            $validated['enabled'] ?? true,
        );

        $productUnit->setRequestTranslations($validated);
        $productUnit->save();

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

        $productUnit = $this->productUnitRepository->update(
            $productUnit,
            $validated['code'],
            $validated['enabled'],
        );

        $productUnit->setRequestTranslations($validated);
        $productUnit->save();

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
