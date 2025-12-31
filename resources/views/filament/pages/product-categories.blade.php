@php
    /** @var \Illuminate\Support\Collection $categories */
@endphp

<x-filament::page>
    @if($categories->isEmpty())
        <p class="text-gray-500">Nincs megjeleníthető kategória.</p>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <ul class="space-y-2">
                @foreach($categories as $category)
                    <li class="text-gray-900 dark:text-gray-100">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-folder class="w-5 h-5 text-gray-400" />
                            <span class="font-medium">{{ $category->name }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-filament::page>

