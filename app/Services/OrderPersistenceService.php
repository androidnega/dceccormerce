<?php

namespace App\Services;

use App\Mail\OrderReceiptMail;
use App\Models\Coupon;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderPersistenceService
{
    public function __construct(
        private readonly DeliveryPricingService $deliveryPricing,
        private readonly CouponService $couponService,
    ) {}

    /**
     * JSON helper for checkout delivery step (AJAX). Requires region + zone for priced options.
     *
     * @param  array<int, array<string, mixed>>  $cart
     * @return array{options: list<array<string, mixed>>, selectedOption: string, zone_label: string}
     */
    public function deliveryOptionsForCart(
        array $cart,
        ?int $regionId,
        ?int $zoneId,
        string $selectedOption,
        ?string $couponCode
    ): array {
        if ($cart === []) {
            return ['options' => [], 'selectedOption' => 'standard', 'zone_label' => ''];
        }

        if ($regionId === null || $regionId < 1 || $zoneId === null || $zoneId < 1) {
            return ['options' => [], 'selectedOption' => 'standard', 'zone_label' => ''];
        }

        $zone = DeliveryZone::query()->active()->whereKey($zoneId)->where('region_id', $regionId)->with('region')->first();
        if ($zone === null) {
            return ['options' => [], 'selectedOption' => 'standard', 'zone_label' => ''];
        }

        $priced = $this->subtotalAndQuantityFromEffectiveCartPrices($cart);
        $itemsSubtotal = $priced['items_subtotal'];
        $totalQty = $priced['total_quantity'];
        $promoDiscountAmount = round($itemsSubtotal * (Promo::activeCartDiscountPercent() / 100), 2);
        $afterPromo = round($itemsSubtotal - $promoDiscountAmount, 2);
        $couponEval = $this->couponService->evaluate($couponCode, max(0.0, $afterPromo));
        $freeDelivery = $this->freeDeliveryActive((bool) ($couponEval['free_delivery'] ?? false));

        $baseFee = (float) $zone->fee;
        $base = $this->baseOptionsFromZoneFee($baseFee);

        $options = $this->deliveryPricing->applyToOptions(
            $base,
            [
                'items_subtotal' => $itemsSubtotal,
                'promo_discount_amount' => $promoDiscountAmount,
                'total_quantity' => $totalQty,
            ]
        );

        if ($freeDelivery) {
            foreach ($options as &$o) {
                $o['price'] = 0.0;
                $o['price_note'] = null;
            }
            unset($o);
        }

        $selected = $selectedOption;
        if ($selected === '' || ! isset($options[$selected])) {
            $selected = array_key_first($options) ?: 'standard';
        }

        $zoneLabel = trim(($zone->region?->name ?? '').' — '.$zone->name);

        return [
            'zone_label' => $zoneLabel,
            'selectedOption' => $selected,
            'options' => array_values(array_map(function (array $row) {
                return [
                    'option' => $row['option'],
                    'method' => $row['method'],
                    'price' => (float) $row['price'],
                    'estimated_time' => $row['estimated_time'] ?? null,
                    'price_note' => $row['price_note'] ?? null,
                ];
            }, $options)),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<int, array<string, mixed>>  $cart
     * @return array{grand_total: float}
     */
    public function quoteGrandTotal(array $validated, array $cart): array
    {
        if ($cart === []) {
            throw ValidationException::withMessages([
                'checkout' => ['Your cart is empty.'],
            ]);
        }

        return DB::transaction(function () use ($validated, $cart) {
            $resolved = $this->resolveCartLines($cart);
            $itemsSubtotal = $this->sumItemsSubtotal($resolved);
            $itemsSubtotal = round($itemsSubtotal, 2);
            $promoDiscountAmount = round($itemsSubtotal * (Promo::activeCartDiscountPercent() / 100), 2);
            $afterPromo = round($itemsSubtotal - $promoDiscountAmount, 2);

            $couponCode = isset($validated['coupon_code']) ? trim((string) $validated['coupon_code']) : '';
            $couponEval = $this->couponService->evaluate($couponCode, max(0.0, $afterPromo));
            $couponDiscount = (float) $couponEval['discount'];
            $totalAmount = round(max(0.0, $afterPromo - $couponDiscount), 2);

            $delOpt = (string) ($validated['delivery_option'] ?? 'standard');
            $freeDelivery = $this->freeDeliveryActive((bool) ($couponEval['free_delivery'] ?? false));

            $deliveryContext = $this->buildDeliveryPricing(
                $validated,
                $delOpt,
                $itemsSubtotal,
                $promoDiscountAmount,
                $resolved,
                $freeDelivery
            );

            $grand = round($totalAmount + $deliveryContext['effective_delivery_price'], 2);

            return ['grand_total' => $grand];
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<int, array<string, mixed>>  $cart
     */
    public function persist(
        array $validated,
        array $cart,
        ?int $userId,
        string $paymentStatus,
        ?string $paystackReference,
        bool $clearSessionCart
    ): Order {
        $paystackReference = $paystackReference !== null && $paystackReference !== '' ? $paystackReference : null;

        return DB::transaction(function () use (
            $validated,
            $cart,
            $userId,
            $paymentStatus,
            $paystackReference,
            $clearSessionCart
        ) {
            if ($paystackReference !== null) {
                $existing = Order::query()
                    ->where('paystack_reference', $paystackReference)
                    ->lockForUpdate()
                    ->first();
                if ($existing !== null) {
                    return $existing;
                }
            }

            if ($cart === []) {
                throw ValidationException::withMessages([
                    'checkout' => ['Your cart is empty.'],
                ]);
            }

            $resolved = $this->resolveCartLines($cart);
            $itemsSubtotal = round($this->sumItemsSubtotal($resolved), 2);
            $promoDiscountAmount = round($itemsSubtotal * (Promo::activeCartDiscountPercent() / 100), 2);
            $afterPromo = round($itemsSubtotal - $promoDiscountAmount, 2);

            $couponCode = isset($validated['coupon_code']) ? trim((string) $validated['coupon_code']) : '';
            $couponModel = null;
            $couponDiscount = 0.0;
            $couponFreeDelivery = false;

            if ($couponCode !== '') {
                $couponModel = Coupon::query()
                    ->active()
                    ->whereRaw('UPPER(code) = ?', [strtoupper($couponCode)])
                    ->lockForUpdate()
                    ->first();

                if ($couponModel === null) {
                    throw ValidationException::withMessages([
                        'coupon_code' => ['This coupon is not valid.'],
                    ]);
                }

                if ($couponModel->usage_limit !== null && $couponModel->used_count >= $couponModel->usage_limit) {
                    throw ValidationException::withMessages([
                        'coupon_code' => ['This coupon has reached its usage limit.'],
                    ]);
                }

                [$couponDiscount, $couponFreeDelivery] = $this->discountFromCoupon($couponModel, max(0.0, $afterPromo));
            }

            $totalAmount = round(max(0.0, $afterPromo - $couponDiscount), 2);

            $delOpt = (string) ($validated['delivery_option'] ?? 'standard');
            $freeDelivery = $this->freeDeliveryActive($couponFreeDelivery);

            $deliveryContext = $this->buildDeliveryPricing(
                $validated,
                $delOpt,
                $itemsSubtotal,
                $promoDiscountAmount,
                $resolved,
                $freeDelivery
            );

            $effectiveDeliveryPrice = $deliveryContext['effective_delivery_price'];
            $deliveryPriceRaw = $deliveryContext['delivery_price_raw'];
            $deliveryZoneLabel = $deliveryContext['delivery_zone_label'];
            $deliveryMethod = $deliveryContext['delivery_method'];
            $regionId = $deliveryContext['region_id'];
            $deliveryZoneId = $deliveryContext['delivery_zone_id'];

            $customerEmail = trim((string) ($validated['email'] ?? ''));
            if ($customerEmail === '' && $userId !== null) {
                $u = User::query()->find($userId);
                $customerEmail = $u !== null ? trim((string) $u->email) : '';
            }

            $accessToken = Str::random(48);

            try {
                $order = Order::query()->create([
                    'user_id' => $userId,
                    'region_id' => $regionId,
                    'delivery_zone_id' => $deliveryZoneId,
                    'total_amount' => round($totalAmount + $effectiveDeliveryPrice, 2),
                    'promo_discount_amount' => $promoDiscountAmount,
                    'discount_amount' => $couponModel !== null ? round($couponDiscount, 2) : null,
                    'coupon_code' => $couponModel !== null ? strtoupper($couponModel->code) : null,
                    'status' => 'pending',
                    'delivery_status' => 'pending',
                    'payment_status' => $paymentStatus,
                    'payment_method' => $validated['payment_method'] ?? 'momo',
                    'paystack_reference' => $paystackReference,
                    'delivery_option' => $validated['delivery_option'],
                    'delivery_method' => $deliveryMethod,
                    'delivery_zone' => $deliveryZoneLabel,
                    'delivery_price' => round($effectiveDeliveryPrice, 2),
                    'delivery_fee' => round($deliveryPriceRaw, 2),
                    'customer_email' => $customerEmail !== '' ? $customerEmail : null,
                    'access_token' => $accessToken,
                ]);
            } catch (QueryException $e) {
                if ($paystackReference !== null && $this->isDuplicatePaystackReferenceViolation($e)) {
                    $dup = Order::query()
                        ->where('paystack_reference', $paystackReference)
                        ->lockForUpdate()
                        ->first();
                    if ($dup !== null) {
                        return $dup;
                    }
                }
                throw $e;
            }

            foreach ($resolved as $row) {
                $product = $row['product'];
                $qty = $row['quantity'];

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->effectivePrice(),
                ]);

                $product->decrement('stock', $qty);
            }

            if ($couponModel !== null) {
                $couponModel->increment('used_count');
            }

            $addressLine = trim((string) ($validated['address'] ?? ''));
            $country = isset($validated['country']) ? trim((string) $validated['country']) : '';
            $cityLine = $deliveryContext['address_city_line'];

            if ($delOpt === 'pickup') {
                if ($addressLine === '') {
                    $addressLine = 'In-store pickup';
                }
            }
            if ($addressLine === '') {
                $addressLine = '—';
            }

            $sameRecipient = (int) ($validated['recipient_same_as_contact'] ?? 0) === 1;

            OrderAddress::query()->create([
                'order_id' => $order->id,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'recipient_name' => $sameRecipient ? null : trim((string) ($validated['recipient_name'] ?? '')),
                'recipient_phone' => $sameRecipient ? null : trim((string) ($validated['recipient_phone'] ?? '')),
                'address' => $addressLine,
                'city' => $cityLine === '' ? null : $cityLine,
                'country' => $country === '' ? null : $country,
            ]);

            if ($clearSessionCart) {
                session()->forget('cart');
            }

            $fresh = $order->fresh();

            DB::afterCommit(function () use ($fresh): void {
                $this->sendReceipt($fresh);
            });

            return $fresh;
        });
    }

    private function sendReceipt(Order $order): void
    {
        $email = trim((string) ($order->customer_email ?? ''));
        if ($email === '') {
            return;
        }

        try {
            Mail::to($email)->send(new OrderReceiptMail($order->load(['items.product', 'address'])));
        } catch (\Throwable $e) {
            Log::error('order_receipt_mail_failed', [
                'order_number' => $order->order_number,
                'email' => $email,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Subtotal after store promo discount, using live product prices (not session cart prices).
     */
    public function afterPromoSubtotalFromEffectiveCartPrices(array $cart): float
    {
        $priced = $this->subtotalAndQuantityFromEffectiveCartPrices($cart);
        $itemsSubtotal = $priced['items_subtotal'];
        $promoDiscountAmount = round($itemsSubtotal * (Promo::activeCartDiscountPercent() / 100), 2);

        return round($itemsSubtotal - $promoDiscountAmount, 2);
    }

    /**
     * @param  array<int, array<string, mixed>>  $cart
     * @return array{items_subtotal: float, total_quantity: int}
     */
    private function subtotalAndQuantityFromEffectiveCartPrices(array $cart): array
    {
        $itemsSubtotal = 0.0;
        $totalQty = 0;
        foreach ($cart as $productId => $line) {
            $pid = (int) $productId;
            $q = (int) ($line['quantity'] ?? 0);
            if ($q < 1) {
                continue;
            }
            $product = Product::query()->find($pid);
            if ($product === null || ! $product->is_active) {
                continue;
            }
            $totalQty += $q;
            $itemsSubtotal += $product->effectivePrice() * $q;
        }

        return [
            'items_subtotal' => round($itemsSubtotal, 2),
            'total_quantity' => $totalQty,
        ];
    }

    private function isDuplicatePaystackReferenceViolation(QueryException $e): bool
    {
        if (($e->errorInfo[0] ?? '') !== '23000') {
            return false;
        }

        $msg = strtolower($e->getMessage());

        return str_contains($msg, 'paystack_reference');
    }

    private function freeDeliveryActive(bool $couponFreeDelivery): bool
    {
        return Promo::hasActiveFreeDeliveryPromo() || $couponFreeDelivery;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  list<array{product: Product, quantity: int}>  $resolved
     * @return array{
     *     effective_delivery_price: float,
     *     delivery_price_raw: float,
     *     delivery_zone_label: string,
     *     delivery_method: string,
     *     region_id: ?int,
     *     delivery_zone_id: ?int,
     *     address_city_line: string
     * }
     */
    private function buildDeliveryPricing(
        array $validated,
        string $delOpt,
        float $itemsSubtotal,
        float $promoDiscountAmount,
        array $resolved,
        bool $freeDelivery
    ): array {
        $totalQty = 0;
        foreach ($resolved as $row) {
            $totalQty += (int) $row['quantity'];
        }

        if ($delOpt === 'pickup') {
            return [
                'effective_delivery_price' => 0.0,
                'delivery_price_raw' => 0.0,
                'delivery_zone_label' => 'Pickup',
                'delivery_method' => 'pickup',
                'region_id' => null,
                'delivery_zone_id' => null,
                'address_city_line' => 'Store pickup',
            ];
        }

        $regionId = isset($validated['region_id']) ? (int) $validated['region_id'] : 0;
        $zoneId = isset($validated['delivery_zone_id']) ? (int) $validated['delivery_zone_id'] : 0;

        if ($regionId < 1 || $zoneId < 1) {
            throw ValidationException::withMessages([
                'delivery_zone_id' => ['Please select a delivery area.'],
            ]);
        }

        $zone = DeliveryZone::query()
            ->active()
            ->whereKey($zoneId)
            ->where('region_id', $regionId)
            ->with('region')
            ->first();

        if ($zone === null) {
            throw ValidationException::withMessages([
                'delivery_zone_id' => ['Selected delivery area is not available.'],
            ]);
        }

        $baseFee = (float) $zone->fee;
        $baseOptions = $this->baseOptionsFromZoneFee($baseFee);

        $deliveryOptions = $this->deliveryPricing->applyToOptions(
            $baseOptions,
            [
                'items_subtotal' => $itemsSubtotal,
                'promo_discount_amount' => $promoDiscountAmount,
                'total_quantity' => $totalQty,
            ]
        );

        $selectedDelivery = $deliveryOptions[$delOpt] ?? null;
        $deliveryPriceRaw = (float) ($selectedDelivery['price'] ?? 0);
        $effectiveDeliveryPrice = $freeDelivery ? 0.0 : $deliveryPriceRaw;
        $deliveryMethod = (string) ($selectedDelivery['method'] ?? ($delOpt === 'pickup' ? 'pickup' : 'rider'));

        $region = $zone->region;
        $label = $region !== null ? $region->name.' — '.$zone->name : $zone->name;

        return [
            'effective_delivery_price' => round($effectiveDeliveryPrice, 2),
            'delivery_price_raw' => round($deliveryPriceRaw, 2),
            'delivery_zone_label' => $label,
            'delivery_method' => $deliveryMethod,
            'region_id' => $region->id ?? $regionId,
            'delivery_zone_id' => $zone->id,
            'address_city_line' => $zone->name,
        ];
    }

    /**
     * @return array<string, array{option: string, method: string, price: float, estimated_time: ?string}>
     */
    private function baseOptionsFromZoneFee(float $zoneFee): array
    {
        $surcharge = (float) config('delivery.express_surcharge_on_zone_fee', 5);

        return [
            'standard' => [
                'option' => 'standard',
                'method' => 'rider',
                'price' => round($zoneFee, 2),
                'estimated_time' => '2–5 business days',
            ],
            'express' => [
                'option' => 'express',
                'method' => 'rider',
                'price' => round($zoneFee + $surcharge, 2),
                'estimated_time' => '1 hour',
            ],
            'pickup' => [
                'option' => 'pickup',
                'method' => 'pickup',
                'price' => 0.0,
                'estimated_time' => 'immediate',
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $cart
     * @return list<array{product: Product, quantity: int}>
     */
    private function resolveCartLines(array $cart): array
    {
        $productIds = array_map('intval', array_keys($cart));
        sort($productIds);
        $resolved = [];

        foreach ($productIds as $productId) {
            $qty = (int) ($cart[$productId]['quantity'] ?? 0);
            if ($qty < 1) {
                continue;
            }

            $product = Product::query()->lockForUpdate()->find($productId);

            if (! $product || ! $product->is_active) {
                throw ValidationException::withMessages([
                    'checkout' => ['Cart contains unavailable products. Please update your cart.'],
                ]);
            }

            if ($qty > $product->stock) {
                throw ValidationException::withMessages([
                    'checkout' => ["Insufficient stock for {$product->name}. Available: {$product->stock}."],
                ]);
            }

            $resolved[] = [
                'product' => $product,
                'quantity' => $qty,
            ];
        }

        if ($resolved === []) {
            throw ValidationException::withMessages([
                'checkout' => ['Your cart is empty.'],
            ]);
        }

        return $resolved;
    }

    /**
     * @param  list<array{product: Product, quantity: int}>  $resolved
     */
    private function sumItemsSubtotal(array $resolved): float
    {
        $sum = 0.0;
        foreach ($resolved as $row) {
            $sum += $row['product']->effectivePrice() * $row['quantity'];
        }

        return $sum;
    }

    /**
     * @return array{0: float, 1: bool}
     */
    private function discountFromCoupon(Coupon $coupon, float $itemsSubtotalAfterPromo): array
    {
        if ($coupon->type === Coupon::TYPE_FREE_DELIVERY) {
            return [0.0, true];
        }

        if ($coupon->type === Coupon::TYPE_PERCENT) {
            $pct = max(0.0, min(100.0, (float) $coupon->value));

            return [round($itemsSubtotalAfterPromo * ($pct / 100), 2), false];
        }

        if ($coupon->type === Coupon::TYPE_FIXED) {
            $fixed = max(0.0, (float) $coupon->value);

            return [round(min($fixed, $itemsSubtotalAfterPromo), 2), false];
        }

        return [0.0, false];
    }
}
