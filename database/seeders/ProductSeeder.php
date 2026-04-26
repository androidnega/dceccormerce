<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\RemoteAssetMirror;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            ProductImage::query()->delete();
            Product::query()->delete();

            $categoryIds = Category::query()->pluck('id', 'slug')->all();

            // Product photos: Wikimedia Commons (CC-licensed). Each file is mirrored into
            // storage/app/public/products so the storefront serves images from this server.
            $items = [
                ['cat' => 'iphones', 'name' => 'iPhone 15 Pro Max', 'price' => 1199, 'stock' => 40, 'imgs' => [
                    'https://upload.wikimedia.org/wikipedia/commons/4/42/Front_of_iPhone_15_Pro_Max.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/4/48/Apple_iPhone_15.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/9/96/IPhone_14_-_3.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/3/3c/IPhone_13.jpg',
                ], 'colors' => ['Natural Titanium', 'Blue', 'Silver', 'Gold']],
                ['cat' => 'iphones', 'name' => 'iPhone 15', 'price' => 899, 'stock' => 55, 'imgs' => [
                    'https://upload.wikimedia.org/wikipedia/commons/4/48/Apple_iPhone_15.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/4/42/Front_of_iPhone_15_Pro_Max.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/3/3c/IPhone_13.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/9/96/IPhone_14_-_3.jpg',
                ], 'colors' => ['Blue', 'Natural Titanium', 'Green', 'Yellow']],
                ['cat' => 'iphones', 'name' => 'iPhone 14', 'price' => 749, 'stock' => 0, 'imgs' => [
                    'https://upload.wikimedia.org/wikipedia/commons/9/96/IPhone_14_-_3.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/4/48/Apple_iPhone_15.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/3/3c/IPhone_13.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/4/42/Front_of_iPhone_15_Pro_Max.jpg',
                ], 'colors' => ['Midnight', 'Starlight', 'Purple', 'Red']],
                ['cat' => 'iphones', 'name' => 'iPhone 13', 'price' => 649, 'stock' => 28, 'imgs' => [
                    'https://upload.wikimedia.org/wikipedia/commons/3/3c/IPhone_13.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/9/96/IPhone_14_-_3.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/4/48/Apple_iPhone_15.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/4/42/Front_of_iPhone_15_Pro_Max.jpg',
                ], 'colors' => ['Graphite', 'Silver', 'Gold', 'Pacific Blue']],

                // MacBooks
                ['cat' => 'macbooks', 'name' => 'MacBook Air M2', 'price' => 1299, 'stock' => 22, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/9/9f/M2_Macbook_Air_Starlight_model.jpg'],
                ['cat' => 'macbooks', 'name' => 'MacBook Pro M3', 'price' => 1999, 'stock' => 18, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/4/40/Apple_MacBook_Pro_%28M3%29.jpg'],
                ['cat' => 'macbooks', 'name' => 'MacBook Air M3', 'price' => 1399, 'stock' => 25, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/1/10/Hardware_PXL_20240701_181416002_%2853829190029%29.jpg'],
                ['cat' => 'macbooks', 'name' => 'MacBook Pro 14-inch M3', 'price' => 2299, 'stock' => 12, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/a/a6/M3_Macbook_Pro_14_inch_Space_Grey_model.jpg'],

                // AirPods
                ['cat' => 'airpods', 'name' => 'AirPods Pro (2nd Gen)', 'price' => 249, 'stock' => 80, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/2/2f/AirPods_Pro_%282nd_generation%29.jpg'],
                ['cat' => 'airpods', 'name' => 'AirPods Max', 'price' => 549, 'stock' => 30, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/8/8e/Apple_AirPods_Max.jpg'],
                ['cat' => 'airpods', 'name' => 'AirPods (3rd Gen)', 'price' => 179, 'stock' => 65, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/0/0c/AirPods_3.jpg'],
                ['cat' => 'airpods', 'name' => 'AirPods (2nd Gen)', 'price' => 129, 'stock' => 90, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Second_generation_AirPods.jpg'],

                // Accessories
                ['cat' => 'accessories', 'name' => 'Apple Watch Series 9', 'price' => 399, 'stock' => 45, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/3/39/Apple_Watch_Series_9_1_2023-11-14.jpg'],
                ['cat' => 'accessories', 'name' => 'MagSafe Charger', 'price' => 39, 'stock' => 120, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/0/01/MagSafe_and_USB-C_Cable_Charger_for_iPhone.jpg'],
                ['cat' => 'accessories', 'name' => 'Apple USB-C Cable', 'price' => 19, 'stock' => 200, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/3/3c/USB_Type-C_Cable_-_iPad_USB-C_Charger_%2845640822114%29.jpg'],
                ['cat' => 'accessories', 'name' => 'iPhone Leather Case', 'price' => 59, 'stock' => 75, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/c/cd/Apple_iPhone_7_in_Apple_leather_case.jpg'],
                ['cat' => 'accessories', 'name' => 'Silicone Case for iPhone', 'price' => 49, 'stock' => 100, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/0/07/Apple_iPhone_17_Silicone_Case_%28Black%29.jpg'],
                ['cat' => 'accessories', 'name' => 'Lightning to USB Cable', 'price' => 29, 'stock' => 150, 'img' => 'https://upload.wikimedia.org/wikipedia/commons/f/fc/Lightning_Cable_%2851826100650%29.jpg'],
            ];

            foreach ($items as $row) {
                $baseSlug = Str::slug($row['name']);
                $product = Product::query()->create([
                    'category_id' => $categoryIds[$row['cat']],
                    'name' => $row['name'],
                    'slug' => Product::uniqueSlug($baseSlug !== '' ? $baseSlug : 'item', null),
                    'description' => 'Genuine Apple product. Premium quality and design.',
                    'price' => $row['price'],
                    'stock' => $row['stock'],
                    'is_active' => true,
                ]);

                $imageUrls = $row['imgs'] ?? (isset($row['img']) ? [$row['img']] : []);
                $colors = $row['colors'] ?? [];
                foreach ($imageUrls as $sort => $url) {
                    $basename = 'p'.$product->id.'-'.$sort;
                    $stored = RemoteAssetMirror::mirrorToPublicDisk($url, 'products', $basename)
                        ?? RemoteAssetMirror::copyPublicAssetToPublicDisk(
                            'images/category-flagship.svg',
                            'products/'.$basename.'-placeholder.svg',
                        );
                    if ($stored === null) {
                        continue;
                    }
                    ProductImage::query()->create([
                        'product_id' => $product->id,
                        'image_path' => $stored,
                        'sort_order' => $sort,
                        'color_label' => $colors[$sort] ?? null,
                    ]);
                }
            }
        });
    }
}
