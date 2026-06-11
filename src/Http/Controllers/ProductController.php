<?php

namespace Molitor\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Molitor\Admin\Traits\HasAdminFilters;
use Molitor\Product\Http\Requests\StoreProductRequest;
use Molitor\Product\Http\Requests\UpdateProductRequest;
use Molitor\Product\Http\Resources\ProductResource;
use Molitor\Product\Http\Resources\ProductUnitSimpleResource;
use Molitor\Product\Models\Product;
use Molitor\Product\Models\ProductUnit;
use Molitor\Product\Repositories\ProductRepositoryInterface;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    use HasAdminFilters;

    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    #[OA\Get(
        path: '/api/admin/product/products',
        summary: 'List all products',
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Product')
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
        $query = Product::with(['productUnit', 'translations']);
        $products = $this->applyAdminFilters($query, $request, ['sku', 'name'])
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    #[OA\Get(
        path: '/api/admin/product/products/select',
        summary: 'Search products for select inputs',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Product')
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
    public function select(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = max(1, min(50, (int) $request->input('per_page', 20)));

        $query = Product::query()->with(['productUnit', 'translations', 'productImages', 'productCategories']);

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('sku', 'like', '%'.$search.'%')
                    ->orWhereHas('translations', function ($translationQuery) use ($search): void {
                        $translationQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $products = $query
            ->orderBy('sku')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    #[OA\Get(
        path: '/api/admin/product/products/create',
        summary: 'Show form for creating a product',
        tags: ['Products'],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    public function create(): JsonResponse
    {
        return response()->json([
            'product_units' => ProductUnitSimpleResource::collection(ProductUnit::all()),
        ]);
    }

    #[OA\Post(
        path: '/api/admin/product/products',
        summary: 'Store a new product',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreProductRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $product = DB::transaction(function () use ($validated): Product {
            $product = $this->productRepository->create(
                $validated['sku'],
                $validated['slug'] ?? null,
                (float) ($validated['price'] ?? 0),
                (bool) ($validated['active'] ?? false),
                $validated['product_unit_id'] ?? null,
            );

            $product->setRequestTranslations($validated);
            $product->save();

            if (array_key_exists('product_images', $validated)) {
                $this->syncProductImages($product, $validated['product_images'] ?? []);
            }

            if (array_key_exists('product_category_ids', $validated)) {
                $product->productCategories()->sync($validated['product_category_ids'] ?? []);
            }

            return $product;
        });

        $product->load(['productUnit', 'translations', 'productImages', 'productCategories']);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => __('product::product.messages.created'),
        ], 201);
    }

    #[OA\Get(
        path: '/api/admin/product/products/{product}',
        summary: 'Display a specific product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        $product->load(['productUnit', 'translations', 'productImages', 'productCategories']);

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    #[OA\Get(
        path: '/api/admin/product/products/{product}/edit',
        summary: 'Show form for editing a product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function edit(Product $product): JsonResponse
    {
        $product->load(['productUnit', 'translations', 'productImages', 'productCategories']);

        return response()->json([
            'data' => new ProductResource($product),
            'product_units' => ProductUnitSimpleResource::collection(ProductUnit::all()),
        ]);
    }

    #[OA\Put(
        path: '/api/admin/product/products/{product}',
        summary: 'Update a product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProductRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        $product = DB::transaction(function () use ($validated, $product): Product {
            $product = $this->productRepository->update(
                $product,
                $validated['sku'],
                $validated['slug'] ?? $product->slug,
                (float) ($validated['price'] ?? $product->price),
                (bool) ($validated['active'] ?? $product->active),
                $validated['product_unit_id'] ?? $product->product_unit_id,
            );

            if (isset($validated['translations'])) {
                foreach ($validated['translations'] as $languageId => $translation) {
                    $product->translations()->updateOrCreate(
                        ['language_id' => $languageId],
                        [
                            'name' => $translation['name'] ?? '',
                            'description' => $translation['description'] ?? null,
                        ]
                    );
                }
            }

            if (array_key_exists('product_images', $validated)) {
                $this->syncProductImages($product, $validated['product_images'] ?? []);
            }

            if (array_key_exists('product_category_ids', $validated)) {
                $product->productCategories()->sync($validated['product_category_ids'] ?? []);
            }

            return $product;
        });

        $product->load(['productUnit', 'translations', 'productImages', 'productCategories']);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => __('product::product.messages.updated'),
        ]);
    }

    #[OA\Delete(
        path: '/api/admin/product/products/{product}',
        summary: 'Delete a product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => __('product::product.messages.deleted'),
        ]);
    }

    /**
     * @param  array<int, array{image_url: string, is_main?: bool, sort?: int}>  $productImages
     */
    private function syncProductImages(Product $product, array $productImages): void
    {
        foreach ($product->productImages()->get() as $productImage) {
            $productImage->delete();
        }

        if ($productImages === []) {
            return;
        }

        $mainImageIndex = collect($productImages)->search(static function (array $productImage): bool {
            return ($productImage['is_main'] ?? false) === true;
        });

        if ($mainImageIndex === false) {
            $mainImageIndex = 0;
        }

        foreach ($productImages as $index => $productImage) {
            $product->productImages()->create([
                'image_url' => $productImage['image_url'],
                'is_main' => $index === $mainImageIndex,
                'sort' => $productImage['sort'] ?? $index,
            ]);
        }
    }
}
