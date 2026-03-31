<?php

use App\Models\HomepageSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Replace legacy "$" in stored homepage promo price lines with Ghana cedis (₵).
     */
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('homepage_settings')) {
            return;
        }

        $rows = DB::table('homepage_settings')->select('id', 'promo_banners')->get();
        foreach ($rows as $row) {
            if ($row->promo_banners === null || $row->promo_banners === '') {
                continue;
            }
            $promo = json_decode($row->promo_banners, true);
            if (! is_array($promo)) {
                continue;
            }
            $changed = false;
            foreach ($promo as $i => $slot) {
                if (! is_array($slot) || ! isset($slot['price_label'])) {
                    continue;
                }
                $next = HomepageSetting::normalizePromoPriceLabel($slot['price_label']);
                if ($next !== $slot['price_label']) {
                    $promo[$i]['price_label'] = $next;
                    $changed = true;
                }
            }
            if ($changed) {
                DB::table('homepage_settings')->where('id', $row->id)->update([
                    'promo_banners' => json_encode($promo),
                ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
