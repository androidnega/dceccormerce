<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function findValidByCode(?string $code): ?Coupon
    {
        if ($code === null || trim($code) === '') {
            return null;
        }

        $normalized = strtoupper(trim($code));

        return Coupon::query()
            ->active()
            ->whereRaw('UPPER(code) = ?', [$normalized])
            ->first();
    }

    /**
     * @return array{discount: float, coupon: ?Coupon, free_delivery: bool}
     */
    public function evaluate(?string $code, float $itemsSubtotalAfterPromo): array
    {
        if ($code === null || trim($code) === '') {
            return ['discount' => 0.0, 'coupon' => null, 'free_delivery' => false];
        }

        $coupon = $this->findValidByCode($code);
        if ($coupon === null) {
            return ['discount' => 0.0, 'coupon' => null, 'free_delivery' => false];
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return ['discount' => 0.0, 'coupon' => null, 'free_delivery' => false];
        }

        if ($coupon->type === Coupon::TYPE_FREE_DELIVERY) {
            return ['discount' => 0.0, 'coupon' => $coupon, 'free_delivery' => true];
        }

        if ($coupon->type === Coupon::TYPE_PERCENT) {
            $pct = max(0.0, min(100.0, (float) $coupon->value));

            return [
                'discount' => round($itemsSubtotalAfterPromo * ($pct / 100), 2),
                'coupon' => $coupon,
                'free_delivery' => false,
            ];
        }

        if ($coupon->type === Coupon::TYPE_FIXED) {
            $fixed = max(0.0, (float) $coupon->value);

            return [
                'discount' => round(min($fixed, $itemsSubtotalAfterPromo), 2),
                'coupon' => $coupon,
                'free_delivery' => false,
            ];
        }

        return ['discount' => 0.0, 'coupon' => null, 'free_delivery' => false];
    }

    public static function normalizeCode(?string $code): string
    {
        return strtoupper(trim((string) $code));
    }
}
