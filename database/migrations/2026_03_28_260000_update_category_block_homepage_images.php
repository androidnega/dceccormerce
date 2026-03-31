<?php

use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $legacyMac = 'images/ss1_copy_1920x.webp';
        $legacyIphone = 'images/apple-iphone-14-product-red-guenstig-gebraucht-kaufen.webp';

        HomepageSection::query()
            ->where('type', HomepageSection::TYPE_CATEGORY_BLOCK)
            ->get()
            ->each(function (HomepageSection $section) use ($legacyMac, $legacyIphone) {
                $config = $section->config ?? [];
                $items = $config['items'] ?? [];
                if (! is_array($items) || count($items) < 2) {
                    return;
                }

                $img0 = (string) ($items[0]['image'] ?? '');
                $img1 = (string) ($items[1]['image'] ?? '');
                $matchesLegacy = ($img0 === $legacyMac && $img1 === $legacyIphone)
                    || ($img0 === '' && $img1 === '');

                if (! $matchesLegacy) {
                    return;
                }

                $next = $items;
                $next[0]['image'] = 'images/category-macbook.png';
                $next[1]['image'] = 'images/category-iphone.png';

                $section->update([
                    'config' => array_merge($config, ['items' => $next]),
                ]);
            });
    }

    public function down(): void
    {
        // Content rollback not applied; re-seed or edit in admin if needed.
    }
};
