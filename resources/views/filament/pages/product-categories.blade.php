@php
    /** @var \Illuminate\Support\Collection $categories */
@endphp

<x-filament::page>
    @if($categories->isEmpty())
        <p class="text-gray-500">Nincs megjeleníthető kategória.</p>
    @else
        <div class="space-y-2">
            @foreach($categories as $category)
                <livewire:product-category-tree-item
                    :category="$category"
                    :level="0"
                    :key="'category-' . $category->id"
                    :wire:key="'category-' . $category->id"
                />
            @endforeach
        </div>
    @endif
</x-filament::page>

