<?php

namespace App\Services;

/**
 * Adjusts zone-based delivery option prices using store policy (config/delivery.php):
 * cart subtotal threshold for free standard delivery, per-unit fee reductions,
 * and volume tiers. Base prices still come from {@see \App\Models\DeliveryRule} per zone.
 */
class DeliveryPricingService
{
    /**
     * @param  array<string, array{option: string, method: string, price: float, estimated_time: ?string}>  $baseOptions
     * @param  array{items_subtotal: float, promo_discount_amount: float, total_quantity: int}  $context
     * @return array<string, array{option: string, method: string, price: float, estimated_time: ?string, price_note: ?string}>
     */
    public function applyToOptions(array $baseOptions, array $context): array
    {
        $itemsSubtotal = (float) ($context['items_subtotal'] ?? 0);
        $promoDiscount = (float) ($context['promo_discount_amount'] ?? 0);
        $qty = max(0, (int) ($context['total_quantity'] ?? 0));

        $compare = (string) config('delivery.free_delivery_compare', 'after_promo');
        $subtotalForThreshold = $compare === 'before_promo'
            ? round($itemsSubtotal, 2)
            : round(max(0.0, $itemsSubtotal - $promoDiscount), 2);

        $freeMin = (float) config('delivery.free_delivery_min_subtotal', 0);
        $thresholdMet = $freeMin > 0 && $subtotalForThreshold >= $freeMin;

        $perUnit = (float) config('delivery.per_unit_fee_reduction', 0);
        $perUnitMaxRatio = (float) config('delivery.per_unit_reduction_max_ratio', 0.5);
        $expressMode = (string) config('delivery.express_when_standard_free', 'surcharge_only');

        $adjusted = [];
        foreach ($baseOptions as $key => $row) {
            $base = (float) ($row['price'] ?? 0);
            $fee = $this->applyPerUnitReduction($base, $qty, $perUnit, $perUnitMaxRatio);
            [$fee, $volumeNote] = $this->applyVolumeTier($fee, $qty);
            $adjusted[$key] = array_merge($row, [
                'price' => $fee,
                'price_note' => $volumeNote,
            ]);
        }

        $stdKey = 'standard';
        $std = (float) ($adjusted[$stdKey]['price'] ?? 0);
        $exp = (float) ($adjusted['express']['price'] ?? 0);

        if ($thresholdMet) {
            $symbol = config('store.currency_symbol', '₵');
            $freeNote = 'Free delivery — order '.$symbol.number_format($freeMin, 2).' +';

            if (isset($adjusted[$stdKey])) {
                $adjusted[$stdKey]['price'] = 0.0;
                $adjusted[$stdKey]['price_note'] = $freeNote;
            }

            if (isset($adjusted['express'])) {
                if ($expressMode === 'surcharge_only') {
                    $adjusted['express']['price'] = round(max(0.0, $exp - $std), 2);
                    if ($adjusted['express']['price'] < 0.0001) {
                        $adjusted['express']['price_note'] = $freeNote.' (express)';
                    } else {
                        $adjusted['express']['price_note'] = 'Express surcharge after free standard';
                    }
                } else {
                    $adjusted['express']['price_note'] = $adjusted['express']['price_note']
                        ?? 'Standard free on this order — express at full rate';
                }
            }
        }

        foreach ($adjusted as $key => &$row) {
            $row['price'] = round((float) ($row['price'] ?? 0), 2);
        }
        unset($row);

        return $adjusted;
    }

    private function applyPerUnitReduction(float $fee, int $qty, float $perUnit, float $maxRatio): float
    {
        if ($fee <= 0 || $qty <= 1 || $perUnit <= 0) {
            return round($fee, 2);
        }

        $reduction = $perUnit * ($qty - 1);
        $cap = $fee * max(0.0, min(1.0, $maxRatio));
        $reduction = min($reduction, $cap);

        return round(max(0.0, $fee - $reduction), 2);
    }

    /**
     * @return array{0: float, 1: ?string}
     */
    private function applyVolumeTier(float $fee, int $qty): array
    {
        if ($fee <= 0) {
            return [$fee, null];
        }

        $tiers = config('delivery.volume_tiers', []);
        if ($tiers === []) {
            return [$fee, null];
        }

        usort($tiers, static function ($a, $b) {
            return ((int) ($b['min_qty'] ?? 0)) <=> ((int) ($a['min_qty'] ?? 0));
        });

        foreach ($tiers as $tier) {
            $minQty = (int) ($tier['min_qty'] ?? 0);
            $pct = (float) ($tier['percent_off'] ?? 0);
            if ($minQty < 1 || $qty < $minQty || $pct <= 0) {
                continue;
            }

            $new = round($fee * (1 - $pct / 100), 2);
            $note = (int) $pct.'% off delivery ('.$minQty.'+ items)';

            return [$new, $note];
        }

        return [$fee, null];
    }
}
