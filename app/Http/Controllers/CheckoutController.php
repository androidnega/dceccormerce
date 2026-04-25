<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\PaystackPendingCheckout;
use App\Models\Promo;
use App\Models\Region;
use App\Services\CouponService;
use App\Services\OrderNotificationService;
use App\Services\OrderPersistenceService;
use App\Services\PaystackFinalizeService;
use App\Services\PaystackService;
use App\Support\CartSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderNotificationService $orderNotifications,
        private readonly PaystackService $paystack,
        private readonly OrderPersistenceService $orderPersistence,
        private readonly PaystackFinalizeService $paystackFinalize,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        CartSession::reconcile();

        if (empty(session('cart', []))) {
            return redirect()->route('cart.index')->withErrors(['checkout' => 'Your cart is empty.']);
        }

        $lines = [];
        $itemsSubtotal = 0.0;

        foreach (session('cart', []) as $productId => $line) {
            $price = (float) $line['price'];
            $qty = (int) $line['quantity'];
            $subtotal = $price * $qty;
            $lines[(int) $productId] = array_merge($line, [
                'subtotal' => $subtotal,
            ]);
            $itemsSubtotal += $subtotal;
        }

        $itemsSubtotal = round($itemsSubtotal, 2);
        $promoDiscountPercent = Promo::activeCartDiscountPercent();
        $promoDiscountAmount = round($itemsSubtotal * ($promoDiscountPercent / 100), 2);
        $baseTotal = round($itemsSubtotal - $promoDiscountAmount, 2);

        $regions = Region::query()->orderBy('name')->get();
        $isPickupOld = old('delivery_option') === 'pickup';
        $defaultRegionId = (int) old('region_id', $regions->first()?->id ?? 0);
        $zones = $defaultRegionId > 0
            ? DeliveryZone::query()->active()->where('region_id', $defaultRegionId)->orderBy('name')->get()
            : collect();
        $defaultZoneId = (int) old('delivery_zone_id', $zones->first()?->id ?? 0);

        $selectedDeliveryOption = (string) old('delivery_option', 'standard');
        $couponCodeOld = old('coupon_code', '');

        $deliveryPrice = 0.0;
        $effectiveDeliveryPrice = 0.0;
        $deliveryOptions = [];

        if ($isPickupOld) {
            $deliveryOptions = [
                'standard' => ['option' => 'standard', 'method' => 'rider', 'price' => 0.0, 'estimated_time' => '—'],
                'express' => ['option' => 'express', 'method' => 'rider', 'price' => 0.0, 'estimated_time' => '—'],
                'pickup' => ['option' => 'pickup', 'method' => 'pickup', 'price' => 0.0, 'estimated_time' => 'immediate'],
            ];
            $selectedDeliveryOption = 'pickup';
            $deliveryPrice = 0.0;
            $effectiveDeliveryPrice = Promo::hasActiveFreeDeliveryPromo() ? 0.0 : 0.0;
        } elseif ($defaultRegionId > 0 && $defaultZoneId > 0) {
            $api = $this->orderPersistence->deliveryOptionsForCart(
                session('cart', []),
                $defaultRegionId,
                $defaultZoneId,
                $selectedDeliveryOption,
                $couponCodeOld !== '' ? (string) $couponCodeOld : null
            );
            foreach ($api['options'] as $row) {
                $deliveryOptions[$row['option']] = $row;
            }
            if (! isset($deliveryOptions[$selectedDeliveryOption])) {
                $selectedDeliveryOption = $api['selectedOption'];
            }
            $deliveryPrice = (float) ($deliveryOptions[$selectedDeliveryOption]['price'] ?? 0);
            $effectiveDeliveryPrice = $deliveryPrice;
        } else {
            $deliveryOptions = [
                'standard' => ['option' => 'standard', 'method' => 'rider', 'price' => 0.0, 'estimated_time' => '—'],
                'express' => ['option' => 'express', 'method' => 'rider', 'price' => 0.0, 'estimated_time' => '—'],
                'pickup' => ['option' => 'pickup', 'method' => 'pickup', 'price' => 0.0, 'estimated_time' => 'immediate'],
            ];
        }

        $total = round($baseTotal + $effectiveDeliveryPrice, 2);

        $user = $request->user();
        $checkoutContactPrefill = $user
            ? [
                'full_name' => (string) $user->name,
                'email' => (string) $user->email,
            ]
            : null;

        return view('checkout.index', [
            'lines' => $lines,
            'itemsSubtotal' => $itemsSubtotal,
            'promoDiscountPercent' => $promoDiscountPercent,
            'promoDiscountAmount' => $promoDiscountAmount,
            'total' => $total,
            'freeDeliveryPromoActive' => Promo::hasActiveFreeDeliveryPromo()
                || $this->couponServiceEvaluateFreeOnly($couponCodeOld, max(0.0, $baseTotal)),
            'regions' => $regions,
            'zones' => $zones,
            'selectedRegionId' => $defaultRegionId,
            'selectedZoneId' => $defaultZoneId,
            'deliveryOptions' => $deliveryOptions,
            'selectedDeliveryOption' => $selectedDeliveryOption,
            'deliveryPrice' => $deliveryPrice,
            'effectiveDeliveryPrice' => $effectiveDeliveryPrice,
            'paystackReady' => paystack_ready(),
            'checkoutContactPrefill' => $checkoutContactPrefill,
        ]);
    }

    private function couponServiceEvaluateFreeOnly(string $couponCode, float $afterPromo): bool
    {
        if (trim($couponCode) === '') {
            return false;
        }
        $eval = app(CouponService::class)->evaluate($couponCode, max(0.0, $afterPromo));

        return (bool) ($eval['free_delivery'] ?? false);
    }

    public function store(Request $request): RedirectResponse
    {
        $isPickup = $request->input('delivery_option') === 'pickup';
        $needsPaystackEmail = $request->input('payment_method') === 'momo' && paystack_ready();

        $validated = $request->validate([
            'email' => array_merge(
                $needsPaystackEmail ? ['required'] : ['nullable'],
                ['string', 'email', 'max:255']
            ),
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'delivery_target' => ['required', 'in:to_me,to_other'],
            'recipient_name' => [
                Rule::requiredIf(fn () => $request->input('delivery_target') === 'to_other'),
                'nullable',
                'string',
                'max:255',
            ],
            'recipient_phone' => [
                Rule::requiredIf(fn () => $request->input('delivery_target') === 'to_other'),
                'nullable',
                'string',
                'max:50',
            ],
            'address' => $isPickup ? ['nullable', 'string', 'max:500'] : ['required', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:100'],
            'region_id' => $isPickup ? ['nullable', 'integer', 'exists:regions,id'] : ['required', 'integer', 'exists:regions,id'],
            'delivery_zone_id' => $isPickup ? ['nullable', 'integer', 'exists:delivery_zones,id'] : ['required', 'integer', 'exists:delivery_zones,id'],
            'delivery_option' => ['required', 'string', 'in:standard,express,pickup'],
            'payment_method' => ['required', 'string', 'in:cod,momo'],
            'coupon_code' => ['nullable', 'string', 'max:64'],
        ]);

        if (! $isPickup && isset($validated['delivery_zone_id'], $validated['region_id'])) {
            $zoneOk = DeliveryZone::query()
                ->active()
                ->whereKey((int) $validated['delivery_zone_id'])
                ->where('region_id', (int) $validated['region_id'])
                ->exists();
            if (! $zoneOk) {
                return redirect()->route('checkout.index')->withErrors(['delivery_zone_id' => 'Selected area does not match the region.'])->withInput();
            }
        }

        $validated['recipient_same_as_contact'] = $validated['delivery_target'] === 'to_me' ? '1' : '0';

        if (empty(session('cart', []))) {
            return redirect()->route('cart.index')->withErrors(['checkout' => 'Your cart is empty.']);
        }

        if ($request->input('payment_method') === 'momo' && ! paystack_ready()) {
            return redirect()
                ->route('checkout.index')
                ->withErrors(['payment_method' => 'Mobile Money online payment is not set up yet. Please choose cash on delivery, or try again after the store enables Paystack.'])
                ->withInput();
        }

        if ($request->input('payment_method') === 'momo' && paystack_ready()) {
            try {
                $quote = $this->orderPersistence->quoteGrandTotal($validated, session('cart', []));
            } catch (ValidationException $e) {
                return redirect()->route('checkout.index')->withErrors($e->errors())->withInput();
            }
            if ($quote['grand_total'] < 0.01) {
                return redirect()
                    ->route('checkout.index')
                    ->withErrors(['checkout' => 'The order total is too small to pay online. Add items or choose cash on delivery.'])
                    ->withInput();
            }

            $expectedPesewas = $this->paystack->amountToPesewas($quote['grand_total']);

            try {
                $callback = route('checkout.paystack.callback');
                $start = $this->paystack->beginCheckout(
                    (string) $validated['email'],
                    $expectedPesewas,
                    $callback,
                    [
                        'name' => $validated['full_name'],
                        'phone' => $validated['phone'],
                    ]
                );
            } catch (\Throwable $e) {
                return redirect()
                    ->route('checkout.index')
                    ->withErrors(['checkout' => 'Could not start payment. Please try again in a moment or use cash on delivery.'])
                    ->withInput();
            }

            if ($start['reference'] === '' || $start['authorization_url'] === '') {
                return redirect()
                    ->route('checkout.index')
                    ->withErrors(['checkout' => 'Payment start failed. Please try again.'])
                    ->withInput();
            }

            PaystackPendingCheckout::query()->create([
                'reference' => $start['reference'],
                'cart_payload' => session('cart', []),
                'validated_payload' => $validated,
                'expected_amount_pesewas' => $start['amount_pesewas'],
                'user_id' => $request->user()?->id,
            ]);

            $request->session()->put('paystack_checkout', [
                'expected_amount_pesewas' => $start['amount_pesewas'],
                'paystack_reference' => $start['reference'],
                'validated' => $validated,
            ]);

            return redirect()->away($start['authorization_url']);
        }

        try {
            $order = $this->orderPersistence->persist(
                $validated,
                session('cart', []),
                $request->user()?->id,
                'unpaid',
                null,
                true
            );
        } catch (ValidationException $e) {
            return redirect()->route('checkout.index')->withErrors($e->errors())->withInput();
        }

        try {
            $this->orderNotifications->notifyOrderPlaced($order->fresh(['address']));
        } catch (\Throwable) {
            //
        }

        return $this->redirectToCheckoutSuccess($order);
    }

    public function paystackCallback(Request $request): RedirectResponse|Response
    {
        $reference = (string) $request->query('reference', $request->query('trxref', ''));
        if ($reference === '') {
            return redirect()
                ->route('checkout.index')
                ->withErrors(['checkout' => 'Payment was not completed. Your cart is unchanged.']);
        }

        $pending = PaystackPendingCheckout::query()->where('reference', $reference)->first();
        if ($pending !== null) {
            $result = $this->paystackFinalize->finalizePaidOrder($reference, true);
            $request->session()->forget('paystack_checkout');
            if (! $result['ok'] || $result['order'] === null) {
                return redirect()
                    ->route('checkout.index')
                    ->withErrors(['checkout' => 'We could not finalize your payment. If you were charged, contact support with reference '.$reference.'.']);
            }

            return $this->redirectToCheckoutSuccess($result['order']);
        }

        Log::warning('paystack_callback_no_pending', ['reference' => $reference]);

        return response()->view('checkout.payment-error', [
            'reference' => $reference,
        ], 422);
    }

    public function success(Request $request, string $order_number): View
    {
        $token = trim((string) $request->query('token', ''));
        abort_if($token === '', 403);

        $order = Order::findByOrderNumberAndAccessToken($order_number, $token);
        abort_if($order === null, 403);

        $order->load(['items.product', 'address']);

        return view('checkout.success', compact('order'));
    }

    public function deliveryOptions(Request $request): JsonResponse
    {
        CartSession::reconcile();
        $cart = session('cart', []);

        $regionId = (int) $request->query('region_id', 0);
        $zoneId = (int) $request->query('delivery_zone_id', 0);
        $selected = (string) $request->query('selected', '');
        $coupon = $request->query('coupon_code');

        $out = $this->orderPersistence->deliveryOptionsForCart(
            $cart,
            $regionId > 0 ? $regionId : null,
            $zoneId > 0 ? $zoneId : null,
            $selected,
            is_string($coupon) ? $coupon : null
        );

        $freePromo = Promo::hasActiveFreeDeliveryPromo();
        $afterPromoSub = $this->orderPersistence->afterPromoSubtotalFromEffectiveCartPrices($cart);
        $freeCoupon = false;
        if (is_string($coupon) && $coupon !== '') {
            $ev = app(CouponService::class)->evaluate($coupon, max(0.0, $afterPromoSub));
            $freeCoupon = (bool) ($ev['free_delivery'] ?? false);
        }

        return response()->json(array_merge($out, [
            'freeDeliveryPromoActive' => $freePromo || $freeCoupon,
        ]));
    }

    public function locationZones(Request $request): JsonResponse
    {
        $regionId = (int) $request->query('region_id', 0);
        if ($regionId < 1) {
            return response()->json(['zones' => []]);
        }

        $zones = DeliveryZone::query()
            ->active()
            ->where('region_id', $regionId)
            ->orderBy('name')
            ->get(['id', 'name', 'fee']);

        return response()->json([
            'zones' => $zones->map(fn (DeliveryZone $z) => [
                'id' => $z->id,
                'name' => $z->name,
                'fee' => (float) $z->fee,
            ])->values(),
        ]);
    }

    private function redirectToCheckoutSuccess(Order $order): RedirectResponse
    {
        return redirect()->route('checkout.success', [
            'order_number' => $order->order_number,
            'token' => $order->access_token,
        ]);
    }
}
