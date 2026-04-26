<?php

use App\Models\HomepageSection;
use App\Models\HomepageSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $pathReplacements = [
        'images/category-macbook.png' => 'images/category-flagship.svg',
        'images/category-iphone.png' => 'images/category-stay-connected.svg',
        'images/category-shop-hero.png' => 'images/logo.svg',
        'images/ss2_copy_900x.webp' => 'images/category-flagship.svg',
        'images/ss1_copy_1920x.webp' => 'images/category-flagship.svg',
        'images/hero-watch-showcase.png' => 'images/category-flagship.svg',
        'images/apple-iphone-14-product-red-guenstig-gebraucht-kaufen.webp' => 'images/category-stay-connected.svg',
        'images/b2_900x.webp' => 'images/logo.svg',
        'images/12_374d37ef-6951-4c61-b46d-af6244139c32_1024x1024.webp' => 'images/category-flagship.svg',
    ];

    public function up(): void
    {
        foreach (HomepageSection::query()->cursor() as $section) {
            $changed = false;
            $image = $section->image;
            if (is_string($image) && $image !== '' && isset($this->pathReplacements[$image])) {
                $section->image = $this->pathReplacements[$image];
                $changed = true;
            }

            $config = is_array($section->config) ? $section->config : [];
            if (isset($config['items']) && is_array($config['items'])) {
                foreach ($config['items'] as $i => $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $img = $item['image'] ?? '';
                    if (is_string($img) && isset($this->pathReplacements[$img])) {
                        $config['items'][$i]['image'] = $this->pathReplacements[$img];
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                $section->config = $config;
                $section->save();
            }
        }

        foreach (HomepageSetting::query()->cursor() as $row) {
            $banners = $row->promo_banners;
            if (! is_array($banners)) {
                continue;
            }
            $changed = false;
            foreach ($banners as $i => $banner) {
                if (! is_array($banner)) {
                    continue;
                }
                $p = $banner['image_path'] ?? '';
                if (is_string($p) && isset($this->pathReplacements[$p])) {
                    $banners[$i]['image_path'] = $this->pathReplacements[$p];
                    $changed = true;
                }
            }
            if ($changed) {
                $row->promo_banners = $banners;
                $row->save();
            }
        }

        if (Schema::hasTable('news_posts')) {
            foreach ($this->pathReplacements as $from => $to) {
                DB::table('news_posts')->where('image_path', $from)->update(['image_path' => $to, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
