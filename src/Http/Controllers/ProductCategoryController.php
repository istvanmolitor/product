<?php

namespace Molitor\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Molitor\Language\Repositories\LanguageRepositoryInterface;
use Molitor\Product\Http\Requests\StoreProductCategoryRequest;
use Molitor\Product\Http\Requests\UpdateProductCategoryRequest;
use Molitor\Product\Http\Resources\ProductCategoryResource;
use Molitor\Product\Models\ProductCategory;
use Molitor\Product\Repositories\ProductCategoryRepositoryInterface;

class ProductCategoryController extends Controller
{
    public function __construct(
        protected ProductCategoryRepositoryInterface $productCategoryRepository,
        protected LanguageRepositoryInterface $languageRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->productCategoryRepository->getAll();

        return response()->json([
            'data' => ProductCategoryResource::collection($categories),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        $categories = $this->productCategoryRepository->getOptions();
        $languages = $this->languageRepository->getEnabledLanguages();

        return response()->json([
            'parent_categories' => $categories,
            'languages' => $languages,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $category = ProductCategory::create($request->validated());

        if ($request->has('translations')) {
            foreach ($request->input('translations') as $languageId => $translation) {
                $category->updateTranslation($languageId, $translation);
            }
        }

        $this->productCategoryRepository->refreshLeftRight();

        return response()->json([
            'message' => 'Product category created successfully',
            'data' => new ProductCategoryResource($category->load('translations')),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory): JsonResponse
    {
        return response()->json([
            'data' => new ProductCategoryResource($productCategory->load('translations')),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory): JsonResponse
    {
        $categories = $this->productCategoryRepository->getOptions();
        $languages = $this->languageRepository->getEnabledLanguages();

        return response()->json([
            'data' => new ProductCategoryResource($productCategory->load('translations')),
            'parent_categories' => $categories,
            'languages' => $languages,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        $productCategory->update($request->validated());

        if ($request->has('translations')) {
            foreach ($request->input('translations') as $languageId => $translation) {
                $productCategory->updateTranslation($languageId, $translation);
            }
        }

        $this->productCategoryRepository->refreshLeftRight();

        return response()->json([
            'message' => 'Product category updated successfully',
            'data' => new ProductCategoryResource($productCategory->load('translations')),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        $this->productCategoryRepository->delete($productCategory);

        return response()->json([
            'message' => 'Product category deleted successfully',
        ]);
    }
}
