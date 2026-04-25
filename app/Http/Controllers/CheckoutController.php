<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRule;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promo;
use App\Services\DeliveryPricingService;
use App\Services\OrderNotificationService;
use App\Services\PaystackService;
use App\Support\CartSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderNotificationService $orderNotifications,
        private readonly DeliveryPricingService $deliveryPricing,
        private readonly PaystackService $paystack,
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

        $city = trim((string) old('city', $request->query('city', '')));
        $deliveryZone = $this->resolveDeliveryZoneFromCity($city);
        $totalQty = 0;
        foreach ($lines as $line) {
            $totalQty += (int) ($line['quantity'] ?? 0);
        }
        $deliveryOptions = $this->deliveryPricing->applyToOptions(
            $this->deliveryOptionsForZone($deliveryZone),
            [
                'items_subtotal' => $itemsSubtotal,
                'promo_discount_amount' => $promoDiscountAmount,
                'total_quantity' => $totalQty,
            ]
        );

        $selectedDeliveryOption = (string) old('delivery_option', array_key_first($deliveryOptions) ?: 'standard');
        if (! isset($deliveryOptions[$selectedDeliveryOption])) {
            $selectedDeliveryOption = array_key_first($deliveryOptions) ?: 'standard';
        }

        $deliveryPrice = (float) ($deliveryOptions[$selectedDeliveryOption]['price'] ?? 0);
        $effectiveDeliveryPrice = Promo::hasActiveFreeDeliveryPromo() ? 0.0 : $deliveryPrice;

        $total = round($baseTotal + $effectiveDeliveryPrice, 2);

        return view('checkout.index', [
            'lines' => $lines,
            'itemsSubtotal' => $itemsSubtotal,
            'promoDiscountPercent' => $promoDiscountPercent,
            'promoDiscountAmount' => $promoDiscountAmount,
            'total' => $total,
            'freeDeliveryPromoActive' => Promo::hasActiveFreeDeliveryPromo(),
            'deliveryZone' => $deliveryZone,
            'deliveryOptions' => $deliveryOptions,
            'selectedDeliveryOption' => $selectedDeliveryOption,
            'deliveryPrice' => $deliveryPrice,
            'effectiveDeliveryPrice' => $effectiveDeliveryPrice,
            'paystackReady' => paystack_ready(),
        ]);
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
            'recipient_same_as_contact' => ['sometimes', 'in:0,1'],
            'recipient_name' => [
                Rule::requiredIf(fn () => ! $request->boolean('recipient_same_as_contact')),
                'nullable',
                'string',
                'max:255',
            ],
            'recipient_phone' => [
                Rule::requiredIf(fn () => ! $request->boolean('recipient_same_as_contact')),
                'nullable',
                'string',
                'max:50',
            ],
            'address' => $isPickup ? ['nullable', 'string', 'max:500'] : ['required', 'string', 'max:500'],
            'city' => $isPickup ? ['nullable', 'string', 'max:100'] : ['required', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'delivery_option' => ['required', 'string', 'in:standard,express,pickup'],
            'payment_method' => ['required', 'string', 'in:cod,momo'],
        ]);

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
                $quote = $this->quoteOrderTotalsForCart($request, $validated);
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

            $request->session()->put('paystack_checkout', [
                'expected_amount_pesewas' => $start['amount_pesewas'],
                'paystack_reference' => $start['reference'],
                'validated' => $validated,
            ]);

            return redirect()->away($start['authorization_url']);
        }

        try {
            $order = $this->persistOrder($validated, $request, 'unpaid', null);
        } catch (ValidationException $e) {
            return redirect()->route('checkout.index')->withErrors($e->errors())->withInput();
        }

        try {
            $this->orderNotifications->notifyOrderPlaced($order->fresh(['address']));
        } catch (\Throwable) {
            //
        }

        return redirect()->route('checkout.success', $order);
    }

    /**
     * Paystack redirects here after the customer authorizes payment (card or mobile money, including MTN, Vodafone, AirtelTigo in Ghana when enabled on the Paystack dashboard).
     */
    public function paystackCallback(Request $request): RedirectResponse
    {
        $reference = (string) $request->query('reference', $request->query('trxref', ''));
        if ($reference === '') {
            return redirect()
                ->route('checkout.index')
                ->withErrors(['checkout' => 'Payment was not completed. Your cart is unchanged.']);
        }

        $state = $request->session()->get('paystack_checkout');
        if (! is_array($state) || ($state['paystack_reference'] ?? null) !== $reference) {
            return redirect()
                ->route('checkout.index')
                ->withErrors(['checkout' => 'Your payment session is missing or expired. If you completed a charge, use reference '.$reference.' when you contact support.']);
        }

        if (($state['expected_amount_pesewas'] ?? 0) < 1) {
            $request->session()->forget('paystack_checkout');

            return redirect()
                ->route('checkout.index')
                ->withErrors(['checkout' => 'Invalid payment session. Please start checkout again.']);
        }

        if (empty(session('cart', []))) {
            $request->session()->forget('paystack_checkout');

            return redirect()
                ->route('cart.index')
                ->withErrors(['checkout' => 'Your cart is empty. If payment was taken, contact support. Reference: '.$reference]);
        }

        $verified = $this->paystack->verifyReference($reference);
        if ($verified === null || (int) ($verified['amount'] ?? 0) !== (int) $state['expected_amount_pesewas']) {
            return redirect()
                ->route('checkout.index')
                ->withErrors(['checkout' => 'Payment could not be verified. If you were charged, contact support. Reference: '.$reference]);
        }

        $validated = is_array($state['validated'] ?? null) ? $state['validated'] : [];
        if ($validated === []) {
            $request->session()->forget('paystack_checkout');

            return redirect()
                ->route('checkout.index')
                ->withErrors(['checkout' => 'Checkout data was lost. If you were charged, contact support. Reference: '.$reference]);
        }

        try {
            $order = $this->persistOrder($validated, $request, 'paid', $reference);
        } catch (ValidationException $e) {
            $request->session()->forget('paystack_checkout');

            return redirect()->route('checkout.index')->withErrors($e->errors());
        }
        $request->session()->forget('paystack_checkout');

        try {
            $this->orderNotifications->notifyOrderPlaced($order->fresh(['address']));
        } catch (\Throwable) {
            //
        }

        return redirect()->route('checkout.success', $order);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function persistOrder(array $validated, Request $request, string $paymentStatus, ?string $paystackReference): Order
    {
        $paystackReference = $paystackReference !== null && $paystackReference !== '' ? $paystackReference : null;

        return DB::transaction(function () use ($validated, $request, $paymentStatus, $paystackReference) {
            $cart = session('cart', []);
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

            $itemsSubtotal = 0.0;

            foreach ($resolved as $row) {
                $unitPrice = $row['product']->effectivePrice();
                $itemsSubtotal += $unitPrice * $row['quantity'];
            }

            $itemsSubtotal = round($itemsSubtotal, 2);
            $promoDiscountAmount = round($itemsSubtotal * (Promo::activeCartDiscountPercent() / 100), 2);
            $totalAmount = round($itemsSubtotal - $promoDiscountAmount, 2);

            $delOpt = (string) ($validated['delivery_option'] ?? 'standard');
            $city = isset($validated['city']) ? trim((string) $validated['city']) : '';
            $deliveryZone = $this->resolveDeliveryZoneFromCity($delOpt === 'pickup' ? 'accra' : $city);
            $totalQty = 0;
            foreach ($resolved as $row) {
                $totalQty += (int) $row['quantity'];
            }
            $deliveryOptions = $this->deliveryPricing->applyToOptions(
                $this->deliveryOptionsForZone($deliveryZone),
                [
                    'items_subtotal' => $itemsSubtotal,
                    'promo_discount_amount' => $promoDiscountAmount,
                    'total_quantity' => $totalQty,
                ]
            );
            $selectedDeliveryOption = (string) ($validated['delivery_option'] ?? 'standard');
            $selectedDelivery = $deliveryOptions[$selectedDeliveryOption] ?? null;

            $deliveryPrice = (float) ($selectedDelivery['price'] ?? 0);
            $effectiveDeliveryPrice = Promo::hasActiveFreeDeliveryPromo() ? 0.0 : $deliveryPrice;
            $deliveryMethod = (string) ($selectedDelivery['method'] ?? ($selectedDeliveryOption === 'pickup' ? 'pickup' : 'rider'));

            $order = Order::query()->create([
                'user_id' => $request->user()?->id,
                'total_amount' => round($totalAmount + $effectiveDeliveryPrice, 2),
                'promo_discount_amount' => $promoDiscountAmount,
                'status' => 'pending',
                'delivery_status' => 'pending',
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'] ?? 'momo',
                'paystack_reference' => $paystackReference,
                'delivery_option' => $validated['delivery_option'],
                'delivery_method' => $deliveryMethod,
                'delivery_zone' => $deliveryZone,
                'delivery_price' => round($effectiveDeliveryPrice, 2),
            ]);

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

            $addressLine = trim((string) ($validated['address'] ?? ''));
            $country = isset($validated['country']) ? trim((string) $validated['country']) : '';
            if ($selectedDeliveryOption === 'pickup') {
                if ($addressLine === '') {
                    $addressLine = 'In-store pickup';
                }
                if ($city === '') {
                    $city = '—';
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
                'city' => $city === '' ? null : $city,
                'country' => $country === '' ? null : $country,
            ]);

            session()->forget('cart');

            return $order->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{grand_total: float}
     */
    private function quoteOrderTotalsForCart(Request $request, array $validated): array
    {
        if (empty(session('cart', []))) {
            throw ValidationException::withMessages([
                'checkout' => ['Your cart is empty.'],
            ]);
        }

        return DB::transaction(function () use ($validated) {
            $cart = session('cart', []);
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

            $itemsSubtotal = 0.0;
            foreach ($resolved as $row) {
                $itemsSubtotal += $row['product']->effectivePrice() * $row['quantity'];
            }
            $itemsSubtotal = round($itemsSubtotal, 2);
            $promoDiscountAmount = round($itemsSubtotal * (Promo::activeCartDiscountPercent() / 100), 2);
            $totalAmount = round($itemsSubtotal - $promoDiscountAmount, 2);

            $delOpt = (string) ($validated['delivery_option'] ?? 'standard');
            $city = isset($validated['city']) ? trim((string) $validated['city']) : '';
            $deliveryZone = $this->resolveDeliveryZoneFromCity($delOpt === 'pickup' ? 'accra' : $city);
            $totalQty = 0;
            foreach ($resolved as $row) {
                $totalQty += (int) $row['quantity'];
            }
            $deliveryOptions = $this->deliveryPricing->applyToOptions(
                $this->deliveryOptionsForZone($deliveryZone),
                [
                    'items_subtotal' => $itemsSubtotal,
                    'promo_discount_amount' => $promoDiscountAmount,
                    'total_quantity' => $totalQty,
                ]
            );
            $selectedDeliveryOption = (string) ($validated['delivery_option'] ?? 'standard');
            $selectedDelivery = $deliveryOptions[$selectedDeliveryOption] ?? null;
            $deliveryPrice = (float) ($selectedDelivery['price'] ?? 0);
            $effectiveDeliveryPrice = Promo::hasActiveFreeDeliveryPromo() ? 0.0 : $deliveryPrice;
            $grand = round($totalAmount + $effectiveDeliveryPrice, 2);

            return [
                'grand_total' => $grand,
            ];
        });
    }

    public function success(Order $order): View
    {
        $order->load(['items.product', 'address']);

        return view('checkout.success', compact('order'));
    }

    public function deliveryOptions(Request $request): JsonResponse
    {
        CartSession::reconcile();
        $cart = session('cart', []);

        $city = trim((string) $request->query('city', ''));
        $zone = $this->resolveDeliveryZoneFromCity($city);

        if ($cart === []) {
            return response()->json([
                'zone' => $zone,
                'selectedOption' => 'standard',
                'options' => [],
            ]);
        }

        $itemsSubtotal = 0.0;
        $totalQty = 0;
        foreach ($cart as $line) {
            $q = (int) ($line['quantity'] ?? 0);
            $totalQty += $q;
            $itemsSubtotal += (float) ($line['price'] ?? 0) * $q;
        }
        $itemsSubtotal = round($itemsSubtotal, 2);
        $promoDiscountAmount = round($itemsSubtotal * (Promo::activeCartDiscountPercent() / 100), 2);

        $options = $this->deliveryPricing->applyToOptions(
            $this->deliveryOptionsForZone($zone),
            [
                'items_subtotal' => $itemsSubtotal,
                'promo_discount_amount' => $promoDiscountAmount,
                'total_quantity' => $totalQty,
            ]
        );

        $selected = (string) $request->query('selected', '');
        if ($selected === '' || ! isset($options[$selected])) {
            $selected = array_key_first($options) ?: 'standard';
        }

        // Shape response to be friendly for JS.
        $out = [
            'zone' => $zone,
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

        if (Promo::hasActiveFreeDeliveryPromo()) {
            foreach ($out['options'] as &$o) {
                $o['price'] = 0.0;
                $o['price_note'] = null;
            }
        }

        return response()->json($out);
    }

    private function resolveDeliveryZoneFromCity(string $city): string
    {
        $c = mb_strtolower(trim($city));

        if ($c === '' || $c === 'outside city' || str_contains($c, 'outside')) {
            return 'Outside City';
        }

        if (str_contains($c, 'accra')) {
            return 'Accra';
        }

        if (str_contains($c, 'takoradi')) {
            return 'Takoradi';
        }

        return 'Outside City';
    }

    /**
     * @return array<string, array{option: string, method: string, price: float, estimated_time: ?string}>
     */
    private function deliveryOptionsForZone(string $zone): array
    {
        $rules = DeliveryRule::query()
            ->where('active', true)
            ->where('zone', $zone)
            ->orderBy('id')
            ->get();

        if ($rules->isEmpty()) {
            // Safe fallback until admins add delivery_rules.
            return [
                'standard' => [
                    'option' => 'standard',
                    'method' => 'rider',
                    'price' => 0.0,
                    'estimated_time' => '2–5 business days',
                ],
                'express' => [
                    'option' => 'express',
                    'method' => 'rider',
                    'price' => 0.0,
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

        $byOption = [];
        foreach ($rules as $rule) {
            if (! isset($byOption[$rule->option])) {
                $byOption[$rule->option] = [
                    'option' => (string) $rule->option,
                    'method' => (string) $rule->method,
                    'price' => (float) $rule->price,
                    'estimated_time' => $rule->estimated_time,
                ];
            }
        }

        // Ensure UI still has all 3 options.
        foreach (['standard', 'express', 'pickup'] as $opt) {
            if (isset($byOption[$opt])) {
                continue;
            }

            $byOption[$opt] = [
                'option' => $opt,
                'method' => $opt === 'pickup' ? 'pickup' : 'rider',
                'price' => 0.0,
                'estimated_time' => $opt === 'pickup' ? 'immediate' : ($opt === 'express' ? '1 hour' : '2–5 business days'),
            ];
        }

        return $byOption;
    }
}
