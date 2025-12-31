<?php

namespace Molitor\Product\Livewire;

use Livewire\Component;
use Molitor\Product\Models\ProductCategory;

class CategoryTreeItem extends Component
{
    public ProductCategory $category;
    public bool $isOpen = false;
    public int $level = 0;

    public function mount(ProductCategory $category, int $level = 0): void
    {
        $this->category = $category;
        $this->level = $level;
    }

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        $children = $this->category
            ->productCategories()
            ->joinTranslation()
            ->orderByTranslation('name')
            ->select('product_categories.*')
            ->get();

        return view('product::livewire.category-tree-item', [
            'children' => $children,
        ]);
    }
}

