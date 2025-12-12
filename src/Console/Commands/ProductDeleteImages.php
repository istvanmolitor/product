<?php

declare(strict_types=1);

namespace Molitor\Product\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Molitor\Product\Models\ProductCategory;
use Molitor\Product\Models\ProductImage;

class ProductDeleteImages extends Command
{
    protected $signature = 'product:delete-images {--force : Do not ask for confirmation} {--dry-run : Only show what would be deleted}';

    protected $description = 'Törli az összes termékképet és termékkategória képet az adatbázisból és a storage-ból is.';

    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Biztosan törölni szeretnéd az ÖSSZES termékképet és kategóriaképet? Ez nem visszavonható.')) {
                $this->warn('Megszakítva.');
                return self::SUCCESS;
            }
        }

        $dryRun = (bool)$this->option('dry-run');

        $productImageFileDeletions = 0;
        $productImageRowDeletions = 0;
        $categoryImageFileDeletions = 0;
        $categoryImageFieldClears = 0;

        // Termékképek törlése (tároló + DB sorok)
        $this->info('Termékképek törlése...');
        ProductImage::query()->orderBy('id')->chunkById(500, function ($images) use ($dryRun, &$productImageFileDeletions, &$productImageRowDeletions) {
            /** @var ProductImage $image */
            foreach ($images as $image) {
                $path = $image->image;
                if ($path) {
                    $normalized = ltrim($path, '/');
                    if ($dryRun) {
                        $this->line("[DRY] Fájl törlés: public://{$normalized}");
                    } else {
                        try {
                            Storage::disk('public')->delete($normalized);
                        } catch (\Throwable $e) {
                            Log::warning('Product image delete failed', ['path' => $normalized, 'error' => $e->getMessage()]);
                        }
                    }
                    $productImageFileDeletions++;
                }

                if ($dryRun) {
                    $this->line("[DRY] DB törlés: product_images.id={$image->id}");
                } else {
                    // töröljük a DB rekordot (fordításokkal együtt)
                    $image->delete();
                }
                $productImageRowDeletions++;
            }
        });

        // Kategória képek törlése (tároló) és mezők nullázása DB-ben
        $this->info('Termékkategória képek törlése...');
        ProductCategory::query()->whereNotNull('image')->orderBy('id')->chunkById(500, function ($categories) use ($dryRun, &$categoryImageFileDeletions, &$categoryImageFieldClears) {
            /** @var ProductCategory $category */
            foreach ($categories as $category) {
                $path = $category->image;
                if ($path) {
                    $normalized = ltrim($path, '/');
                    if ($dryRun) {
                        $this->line("[DRY] Fájl törlés: public://{$normalized}");
                    } else {
                        try {
                            Storage::disk('public')->delete($normalized);
                        } catch (\Throwable $e) {
                            Log::warning('Category image delete failed', ['path' => $normalized, 'error' => $e->getMessage()]);
                        }
                    }
                    $categoryImageFileDeletions++;
                }

                if ($dryRun) {
                    $this->line("[DRY] DB frissítés: product_categories.id={$category->id} image=null, image_url=null");
                } else {
                    $category->image = null;
                    $category->image_url = null;
                    $category->save();
                }
                $categoryImageFieldClears++;
            }
        });

        $this->newLine();
        $this->info('Összegzés:');
        $this->line("Termék képfájl törlések: {$productImageFileDeletions}");
        $this->line("Termékkép rekord törlések: {$productImageRowDeletions}");
        $this->line("Kategória képfájl törlések: {$categoryImageFileDeletions}");
        $this->line("Kategória kép mezők nullázva: {$categoryImageFieldClears}");

        if ($dryRun) {
            $this->warn('DRY-RUN: nem történt tényleges törlés. Távolítsd el a --dry-run opciót a végrehajtáshoz.');
        }

        return self::SUCCESS;
    }
}
