<?php

namespace Molitor\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Molitor\Product\DataTables\ProductDataTable;
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
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function index(ProductDataTable $dataTable): AnonymousResourceCollection
    {
        return $dataTable->getResponse();
    }

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

    public function create(): JsonResponse
    {
        return response()->json([
            'product_units' => ProductUnitSimpleResource::collection(ProductUnit::all()),
        ]);
    }

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

    public function show(Product $product): JsonResponse
    {
        $product->load(['productUnit', 'translations', 'productImages', 'productCategories']);

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    public function edit(Product $product): JsonResponse
    {
        $product->load(['productUnit', 'translations', 'productImages', 'productCategories']);

        return response()->json([
            'data' => new ProductResource($product),
            'product_units' => ProductUnitSimpleResource::collection(ProductUnit::all()),
        ]);
    }

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

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => __('product::product.messages.deleted'),
        ]);
    }
    
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
