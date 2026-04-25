<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Free delivery — minimum cart subtotal
    |--------------------------------------------------------------------------
    |
    | When the cart meets this threshold, standard delivery is waived (₵0).
    | Express typically becomes “surcharge only” (express minus standard) so
    | customers only pay the speed premium. Pickup stays as configured (often 0).
    |
    | Compare against subtotal after the active cart promo discount when
    | "free_delivery_compare" is "after_promo" (recommended).
    |
    */
    'free_delivery_min_subtotal' => (float) env(
        'DELIVERY_FREE_MIN_SUBTOTAL',
        env('STORE_FREE_SHIPPING_MIN', 100)
    ),

    /** "after_promo" | "before_promo" */
    'free_delivery_compare' => env('DELIVERY_FREE_COMPARE', 'after_promo'),

    /*
    |--------------------------------------------------------------------------
    | Quantity-based fee reduction (before volume tiers)
    |--------------------------------------------------------------------------
    |
    | For each unit beyond the first, this amount (in GHS) is subtracted from
    | the delivery fee for that option. Capped so the reduction cannot exceed
    | "per_unit_reduction_max_ratio" of the pre-reduction fee.
    |
    */
    'per_unit_fee_reduction' => (float) env('DELIVERY_PER_UNIT_FEE_REDUCTION', 0),

    /** Max fraction (0–1) of the fee that per-unit reduction can remove. */
    'per_unit_reduction_max_ratio' => (float) env('DELIVERY_PER_UNIT_MAX_RATIO', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Volume tiers — percent off calculated delivery fee
    |--------------------------------------------------------------------------
    |
    | Applied after per-unit reduction. Use the single best matching tier
    | (highest min_qty that the cart still satisfies).
    |
    | Example: [ ['min_qty' => 3, 'percent_off' => 5], ['min_qty' => 6, 'percent_off' => 12] ]
    |
    */
    'volume_tiers' => [
        // Example: ['min_qty' => 3, 'percent_off' => 5.0],
    ],

    /*
    |--------------------------------------------------------------------------
    | Express when standard is free (threshold met)
    |--------------------------------------------------------------------------
    |
    | "surcharge_only" — customer pays max(0, express_adj - standard_adj)
    | "full" — express keeps its adjusted fee (no waiver on express)
    |
    */
    'express_when_standard_free' => env('DELIVERY_EXPRESS_WHEN_STANDARD_FREE', 'surcharge_only'),

    /** Added to zone base fee for express (GHS), when using location delivery_zones. */
    'express_surcharge_on_zone_fee' => (float) env('DELIVERY_EXPRESS_ZONE_SURCHARGE', 5),

];
