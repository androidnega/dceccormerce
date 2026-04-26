<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Support\RemoteAssetMirror;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        if (HeroSlide::query()->exists()) {
            return;
        }

        $slides = [
            [
                'sort_order' => 0,
                'subheading' => 'All new 13-inch & 15-inch',
                'headline' => 'MacBook with',
                'headline_line2' => 'retina display',
                'source' => 'ss1_copy_1920x.webp',
                'mirror_url' => 'https://upload.wikimedia.org/wikipedia/commons/9/9f/M2_Macbook_Air_Starlight_model.jpg',
                'background_hex' => '#eff6ff',
            ],
            [
                'sort_order' => 1,
                'subheading' => 'Liquid Retina. Pro performance.',
                'headline' => 'iPad Pro',
                'headline_line2' => 'Supercharged by M4',
                'source' => 'ss2_copy_900x.webp',
                'mirror_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/48/Apple_iPhone_15.jpg',
                'background_hex' => '#f8fafc',
            ],
            [
                'sort_order' => 2,
                'subheading' => '4K HDR. Dolby Atmos.',
                'headline' => 'Apple TV 4K',
                'headline_line2' => 'The best of TV',
                'source' => 'ss3_copy_1920x.webp',
                'mirror_url' => 'https://upload.wikimedia.org/wikipedia/commons/8/8e/Apple_AirPods_Max.jpg',
                'background_hex' => '#f1f5f9',
            ],
        ];

        foreach ($slides as $def) {
            $relative = null;
            $localSrc = public_path('images/'.$def['source']);
            if (File::isFile($localSrc)) {
                $dest = 'hero-slides/'.$def['source'];
                Storage::disk('public')->put($dest, File::get($localSrc));
                $relative = $dest;
            } else {
                $basename = 'slide-'.$def['sort_order'];
                $relative = RemoteAssetMirror::mirrorToPublicDisk($def['mirror_url'], 'hero-slides', $basename)
                    ?? RemoteAssetMirror::copyPublicAssetToPublicDisk(
                        'images/category-flagship.svg',
                        'hero-slides/'.$basename.'-placeholder.svg',
                    );
            }

            if ($relative === null) {
                $this->command?->warn("HeroSlideSeeder: could not store image for sort_order {$def['sort_order']}, skipping.");

                continue;
            }

            HeroSlide::query()->create([
                'sort_order' => $def['sort_order'],
                'subheading' => $def['subheading'],
                'headline' => $def['headline'],
                'headline_line2' => $def['headline_line2'],
                'cta_label' => 'Shop now',
                'cta_url' => '#store-search',
                'product_id' => null,
                'image_path' => $relative,
                'background_hex' => $def['background_hex'] ?? '#f1f5f9',
                'is_active' => true,
            ]);
        }
    }
}
