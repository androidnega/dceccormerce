@extends('layout')

@section('title', 'Checkout — ' . config('app.name'))
@section('main_class', 'w-full flex-1 bg-slate-50/80')

@section('content')
    <div class="store-box pb-16 pt-8 sm:pb-20 sm:pt-10">
        <header class="border-b border-slate-200/90 pb-8">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Checkout</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">Complete your order in one place. All prices in {{ config('store.currency_code') }}.</p>

            <nav class="checkout-progress mt-8 flex flex-wrap items-center gap-x-2 gap-y-2 text-[11px] font-medium uppercase tracking-[0.12em] text-slate-500 sm:text-xs" aria-label="Checkout progress">
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-slate-600">Cart</span>
                <span class="text-slate-300" aria-hidden="true">→</span>
                <span class="rounded-full bg-[#0057b8] px-3 py-1.5 font-semibold text-white shadow-sm">Info &amp; address</span>
                <span class="text-slate-300" aria-hidden="true">→</span>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-slate-600" id="nav-step-payment">Pay</span>
                <span class="text-slate-300" aria-hidden="true">→</span>
                <span class="rounded-full border border-dashed border-slate-200 bg-slate-50 px-3 py-1.5 text-slate-500">Complete</span>
            </nav>
        </header>

        <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-7">
                @php
                    $paymentMethodOld = old('payment_method', 'cod');
                    if (! $paystackReady && $paymentMethodOld === 'momo') {
                        $paymentMethodOld = 'cod';
                    }
                @endphp
                <form action="{{ route('checkout.store') }}" method="post" class="checkout-form space-y-8" id="checkout-form">
                    @csrf

                    <section id="checkout-info" class="scroll-mt-24 rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8">
                        <div class="flex items-baseline gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">1</span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Contact &amp; delivery</h2>
                                <p class="mt-1 text-sm text-slate-600"><span class="font-medium text-slate-800">Guest checkout</span> — no account or email required. Tell us who to reach about the order and where it should go.</p>
                            </div>
                        </div>

                        <div class="mt-8 space-y-10">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Your contact</h3>
                                <p class="mt-1 text-sm text-slate-600">Who is placing this order — we&apos;ll use this for order updates and payment questions.</p>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-slate-700">Email
                                            @if($paystackReady)
                                                <span class="text-sm font-normal text-slate-500">(required for Mobile Money — used by Paystack)</span>
                                            @endif
                                        </label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}" @if($paystackReady) data-email-paystack="1" @endif autocomplete="email" placeholder="you@example.com"
                                            class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('email') border-red-500 ring-1 ring-red-500 @enderror">
                                        @if(!$paystackReady)
                                            <p class="mt-1 text-xs text-slate-500">Optional for cash on delivery. Required when you pay with Mobile Money online.</p>
                                        @endif
                                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="full_name" class="block text-sm font-medium text-slate-700">Full name <span class="text-red-600">*</span></label>
                                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required autocomplete="name"
                                            class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('full_name') border-red-500 ring-1 ring-red-500 @enderror">
                                        @error('full_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-slate-700">Phone <span class="text-red-600">*</span></label>
                                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required autocomplete="tel"
                                            class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('phone') border-red-500 ring-1 ring-red-500 @enderror">
                                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-10">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Delivery address &amp; recipient</h3>
                                <p class="mt-1 text-sm text-slate-600" id="delivery-hint">Choose where the order should go. For <strong>delivery</strong>, the street and city/area are required. For <strong>store pickup</strong>, you can leave address blank.</p>

                                <div class="mt-4 rounded-xl border border-slate-200/90 bg-slate-50/60 p-4">
                                    <input type="hidden" name="recipient_same_as_contact" value="0">
                                    <label class="flex cursor-pointer items-start gap-3">
                                        <input type="checkbox" name="recipient_same_as_contact" value="1" id="recipient_same_as_contact" class="mt-1 rounded border-slate-300 text-[#0057b8] focus:ring-[#0057b8]"
                                            @checked(old('recipient_same_as_contact', '1') === '1' || old('recipient_same_as_contact') === true)>
                                        <span class="text-sm text-slate-800"><span class="font-medium">I am receiving this order</span> <span class="text-slate-500">(deliver to me at my contact number)</span></span>
                                    </label>
                                </div>

                                <div id="recipient-fields" class="mt-4 space-y-4 @if(old('recipient_same_as_contact', '1') === '1' || old('recipient_same_as_contact') === true) hidden @endif">
                                    <div>
                                        <label for="recipient_name" class="block text-sm font-medium text-slate-700">Recipient name <span class="text-red-600">*</span></label>
                                        <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" data-recipient-input
                                            class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('recipient_name') border-red-500 ring-1 ring-red-500 @enderror">
                                        @error('recipient_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="recipient_phone" class="block text-sm font-medium text-slate-700">Recipient phone <span class="text-red-600">*</span></label>
                                        <input type="tel" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" data-recipient-input autocomplete="tel"
                                            class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('recipient_phone') border-red-500 ring-1 ring-red-500 @enderror">
                                        @error('recipient_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <div class="mt-6 space-y-4">
                                    <div>
                                        <label for="address" class="block text-sm font-medium text-slate-700"><span id="address-label-main">Delivery street address</span> <span id="address-req" class="text-red-600">*</span></label>
                                        <input type="text" name="address" id="address" value="{{ old('address') }}" data-address-input autocomplete="street-address" placeholder="House / building, street, area, GPS description"
                                            class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('address') border-red-500 ring-1 ring-red-500 @enderror">
                                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="city" class="block text-sm font-medium text-slate-700" id="city-label">City or area <span id="city-req" class="text-red-600">*</span></label>
                                            <p class="mt-0.5 text-xs text-slate-500" id="city-hint">Used to calculate delivery. Use “Accra”, “Takoradi”, or <span class="whitespace-nowrap">“Outside city”</span> if you are outside our main zones.</p>
                                            <input type="text" name="city" id="city" value="{{ old('city') }}" autocomplete="address-level2" placeholder="e.g. Accra, East Legon"
                                                class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('city') border-red-500 ring-1 ring-red-500 @enderror">
                                            @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label for="country" class="block text-sm font-medium text-slate-700">Country <span class="font-normal text-slate-400">(optional)</span></label>
                                            <input type="text" name="country" id="country" value="{{ old('country') }}" autocomplete="country-name"
                                                class="store-input-focus mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 @error('country') border-red-500 ring-1 ring-red-500 @enderror">
                                            @error('country')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="checkout-delivery" class="scroll-mt-24 rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8">
                        <div class="flex items-baseline gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">2</span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Delivery method</h2>
                                <p class="mt-1 text-sm text-slate-600">How should we deliver your order?</p>
                            </div>
                        </div>
                        <div class="mt-6 space-y-3" id="delivery-options-list">
                            @foreach ($deliveryOptions as $optKey => $opt)
                                @php
                                    $displayPrice = $freeDeliveryPromoActive ? 0.0 : (float) ($opt['price'] ?? 0.0);
                                    $checked = old('delivery_option', $selectedDeliveryOption) === $optKey;
                                @endphp
                                <label class="checkout-option-label flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/40 p-4 transition hover:border-slate-300 has-[:checked]:border-[#0057b8] has-[:checked]:bg-[#0057b8]/[0.06] has-[:checked]:ring-2 has-[:checked]:ring-[#0057b8]/20">
                                    <input
                                        type="radio"
                                        name="delivery_option"
                                        value="{{ $optKey }}"
                                        class="mt-1 text-[#0057b8] focus:ring-[#0057b8]"
                                        data-delivery-price="{{ $displayPrice }}"
                                        @checked($checked)
                                    >
                                    <span>
                                        <span class="font-medium text-slate-900">
                                            @switch($optKey)
                                                @case('standard') Standard delivery @break
                                                @case('express') Express delivery @break
                                                @case('pickup') Store pickup @break
                                                @default {{ $optKey }} @break
                                            @endswitch
                                        </span>
                                        <span class="mt-0.5 block text-sm text-slate-600">
                                            {{ $opt['estimated_time'] ?? '' }} · {{ format_ghs($displayPrice) }}
                                        </span>
                                        @if (! $freeDeliveryPromoActive && ! empty($opt['price_note']))
                                            <span class="mt-1 block text-xs font-medium leading-snug text-emerald-800">{{ $opt['price_note'] }}</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('delivery_option')<p class="mt-3 text-sm text-red-600">{{ $message }}</p>@enderror
                    </section>

                    <section id="checkout-payment" class="scroll-mt-24 rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8">
                        <div class="flex items-baseline gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">3</span>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Payment method</h2>
                                <p class="mt-1 text-sm text-slate-600">Choose how you’ll pay.</p>
                            </div>
                        </div>
                        <div class="mt-6 space-y-3">
                            <label class="checkout-option-label flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3.5 text-sm font-medium text-slate-800 transition hover:border-slate-300 has-[:checked]:border-[#0057b8] has-[:checked]:bg-[#0057b8]/[0.06] has-[:checked]:ring-2 has-[:checked]:ring-[#0057b8]/20">
                                <input type="radio" name="payment_method" value="cod" class="text-[#0057b8] focus:ring-[#0057b8] js-checkout-payment" @checked($paymentMethodOld === 'cod')>
                                <span>Pay on delivery (cash when you receive the order)</span>
                            </label>
                            <label class="checkout-option-label flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3.5 text-sm text-slate-800 transition {{ $paystackReady ? 'cursor-pointer hover:border-slate-300' : 'cursor-not-allowed opacity-60' }} has-[:checked]:border-[#0057b8] has-[:checked]:bg-[#0057b8]/[0.06] has-[:checked]:ring-2 has-[:checked]:ring-[#0057b8]/20">
                                <input type="radio" name="payment_method" value="momo" class="js-checkout-payment mt-0.5 text-[#0057b8] focus:ring-[#0057b8]" @disabled(! $paystackReady) @checked($paystackReady && $paymentMethodOld === 'momo')>
                                <span>
                                    <span class="font-medium">Mobile Money</span> <span class="text-slate-500">(Paystack — you’ll be taken to a secure page to pay with your wallet or card; MTN, Vodafone, AirtelTigo, etc. where enabled)</span>
                                    @if (! $paystackReady)
                                        <span class="mt-1 block text-xs text-amber-800">This option will be available after the store connects Paystack in the admin.</span>
                                    @endif
                                </span>
                            </label>
                        </div>
                        @error('payment_method')<p class="mt-3 text-sm text-red-600">{{ $message }}</p>@enderror

                        <p class="mt-3 text-xs text-slate-500" id="checkout-email-note" @if($paystackReady) hidden @endif>With cash on delivery, you can continue without a payment email. For Mobile Money online, a valid email is required.</p>

                        <button type="submit" id="checkout-submit" class="store-btn-press mt-8 w-full rounded-xl bg-[#0057b8] py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#00479a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0057b8] focus-visible:ring-offset-2">
                            Place order
                        </button>
                    </section>
                </form>
            </div>

            <aside class="lg:col-span-5">
                <div class="sticky top-24 space-y-5 rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Order summary</h2>
                        <p class="mt-1 text-xs text-slate-500">Review totals before placing your order.</p>
                    </div>
                    @if ($freeDeliveryPromoActive)
                        <p class="rounded-xl border border-emerald-200/80 bg-emerald-50/90 px-3.5 py-2.5 text-xs font-medium leading-snug text-emerald-900">Free delivery promo is active — waived delivery on qualifying orders.</p>
                    @endif
                    @if (($promoDiscountPercent ?? 0) > 0)
                        <p class="text-xs leading-relaxed text-slate-600">Store promo: <span class="font-semibold text-slate-900">extra {{ $promoDiscountPercent }}% off</span> your cart subtotal.</p>
                    @endif
                    <ul class="divide-y divide-slate-100">
                        @foreach ($lines as $productId => $line)
                            <li class="flex justify-between gap-3 py-3 text-sm first:pt-0">
                                <span class="text-slate-700">{{ $line['name'] }} <span class="text-slate-400">×</span> {{ $line['quantity'] }}</span>
                                <span class="shrink-0 font-medium tabular-nums text-slate-900">{{ format_ghs($line['subtotal']) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <dl class="space-y-2.5 border-t border-slate-200 pt-5 text-sm">
                        <div class="flex justify-between gap-3 text-slate-600">
                            <dt>Subtotal</dt>
                            <dd class="tabular-nums font-medium text-slate-900">{{ format_ghs($itemsSubtotal) }}</dd>
                        </div>
                        @if (($promoDiscountAmount ?? 0) > 0.009)
                            <div class="flex justify-between gap-3 text-emerald-800">
                                <dt>Promo ({{ $promoDiscountPercent }}% off)</dt>
                                <dd class="tabular-nums font-semibold">−{{ format_ghs($promoDiscountAmount) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-3 border-t border-slate-100 pt-3 text-slate-600">
                            <dt>Delivery</dt>
                            <dd class="tabular-nums font-medium text-slate-900" id="delivery-fee-amount">
                                {{ format_ghs((float) $effectiveDeliveryPrice) }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-3 border-t border-slate-200 pt-4 text-lg font-semibold text-slate-900">
                            <dt>Total</dt>
                            <dd class="tabular-nums tracking-tight" id="checkout-total-amount">{{ format_ghs($total) }}</dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
@push('scripts')
    <script>
        (function () {
            var paystackReady = @json($paystackReady);
            var addressEl = document.getElementById('address');
            var cityEl = document.getElementById('city');
            var emailEl = document.getElementById('email');
            var form = document.getElementById('checkout-form');
            var submitBtn = document.getElementById('checkout-submit');
            var emailNote = document.getElementById('checkout-email-note');
            var navPay = document.getElementById('nav-step-payment');

            function isPickup() {
                return !!(form && form.querySelector('input[name="delivery_option"][value="pickup"]:checked'));
            }
            function isMomo() {
                return !!(form && form.querySelector('input[name="payment_method"][value="momo"]:checked'));
            }
            function syncAddressAndCityRequired() {
                if (!addressEl || !cityEl) return;
                if (isPickup()) {
                    addressEl.removeAttribute('required');
                    cityEl.removeAttribute('required');
                } else {
                    addressEl.setAttribute('required', 'required');
                    cityEl.setAttribute('required', 'required');
                }
            }
            function syncEmail() {
                if (!emailEl) return;
                if (paystackReady && isMomo()) {
                    emailEl.setAttribute('required', 'required');
                } else {
                    emailEl.removeAttribute('required');
                }
            }
            function syncPayButton() {
                if (!submitBtn || !form) return;
                if (paystackReady && isMomo()) {
                    submitBtn.textContent = 'Continue to secure payment (Paystack)';
                    if (navPay) {
                        navPay.classList.add('bg-[#0057b8]', 'font-semibold', 'text-white', 'shadow-sm');
                        navPay.classList.remove('border', 'bg-white', 'text-slate-600');
                    }
                } else {
                    submitBtn.textContent = 'Place order';
                    if (navPay) {
                        navPay.classList.remove('bg-[#0057b8]', 'font-semibold', 'text-white', 'shadow-sm');
                        navPay.classList.add('border', 'border-slate-200', 'bg-white', 'text-slate-600');
                    }
                }
            }
            function syncEmailNote() {
                if (!emailNote) return;
                if (paystackReady && isMomo()) {
                    emailNote.removeAttribute('hidden');
                    emailNote.textContent = 'You will be redirected to Paystack to pay with mobile money (or card). Use the same email for your receipt.';
                } else {
                    if (paystackReady) emailNote.setAttribute('hidden', 'hidden');
                    else {
                        emailNote.removeAttribute('hidden');
                        emailNote.textContent = 'With cash on delivery, you can continue without a payment email. For Mobile Money online, a valid email is required.';
                    }
                }
            }
            if (form) {
                form.addEventListener('change', function (e) {
                    if (!e.target) return;
                    if (e.target.name === 'delivery_option' || e.target.name === 'payment_method') {
                        syncAddressAndCityRequired();
                        syncEmail();
                        syncPayButton();
                        syncEmailNote();
                    }
                });
                syncAddressAndCityRequired();
                syncEmail();
                syncPayButton();
                syncEmailNote();
            }
        })();
        (function () {
            var cb = document.getElementById('recipient_same_as_contact');
            var wrap = document.getElementById('recipient-fields');
            if (cb && wrap) {
                var inputs = wrap.querySelectorAll('[data-recipient-input]');
                function syncRecipient() {
                    var same = cb.checked;
                    wrap.classList.toggle('hidden', same);
                    inputs.forEach(function (el) {
                        el.required = !same;
                    });
                }
                cb.addEventListener('change', syncRecipient);
                syncRecipient();
            }
        })();
        (function () {
            var cityInput = document.getElementById('city');
            var list = document.getElementById('delivery-options-list');
            if (!cityInput || !list) return;

            var endpoint = @json(route('checkout.delivery-options'));
            var currencySymbol = @json(config('store.currency_symbol', '₵'));
            var baseTotal = Number(@json((float) $itemsSubtotal - (float) $promoDiscountAmount));

            function formatGhs(amount) {
                var n = Number(amount) || 0;
                return currencySymbol + n.toFixed(2);
            }

            function getSelectedPrice() {
                var selected = list.querySelector('input[name="delivery_option"]:checked');
                if (!selected) return 0;
                return Number(selected.dataset.deliveryPrice || '0');
            }

            function setTotals(deliveryPrice) {
                var deliveryEl = document.getElementById('delivery-fee-amount');
                var totalEl = document.getElementById('checkout-total-amount');
                if (!deliveryEl || !totalEl) return;

                var dp = Number(deliveryPrice) || 0;
                deliveryEl.textContent = formatGhs(dp);
                totalEl.textContent = formatGhs(baseTotal + dp);
            }

            list.addEventListener('change', function (e) {
                if (!e.target || e.target.name !== 'delivery_option') return;
                setTotals(getSelectedPrice());
            });

            var debounce = null;
            function labelForOption(opt) {
                switch (opt) {
                    case 'standard': return 'Standard delivery';
                    case 'express': return 'Express delivery';
                    case 'pickup': return 'Store pickup';
                    default: return opt;
                }
            }

            function rebuildOptions(data) {
                var selectedOption = data.selectedOption || 'standard';
                var options = data.options || [];

                list.innerHTML = options.map(function (o) {
                    var isSelected = String(o.option) === String(selectedOption);
                    var est = o.estimated_time || '';
                    var price = formatGhs(o.price);
                    var note = o.price_note ? '<span class="mt-1 block text-xs font-medium leading-snug text-emerald-800">' + String(o.price_note).replace(/</g, '&lt;') + '</span>' : '';
                    return '<label class="checkout-option-label flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/40 p-4 transition hover:border-slate-300 has-[:checked]:border-[#0057b8] has-[:checked]:bg-[#0057b8]/[0.06] has-[:checked]:ring-2 has-[:checked]:ring-[#0057b8]/20">' +
                        '<input type="radio" name="delivery_option" value="' + o.option + '" class="mt-1 text-[#0057b8] focus:ring-[#0057b8]" data-delivery-price="' + (Number(o.price) || 0) + '" ' + (isSelected ? 'checked' : '') + '>' +
                        '<span>' +
                            '<span class="font-medium text-slate-900">' + labelForOption(o.option) + '</span>' +
                            '<span class="mt-0.5 block text-sm text-slate-600">' + est + ' · ' + price + '</span>' +
                            note +
                        '</span>' +
                    '</label>';
                }).join('');

                setTotals(getSelectedPrice());
            }

            async function fetchOptions() {
                if (!cityInput) return;
                var city = cityInput.value || '';
                var selected = (list.querySelector('input[name="delivery_option"]:checked') || {}).value || 'standard';

                var params = new URLSearchParams({ city: city, selected: selected });
                var res = await fetch(endpoint + '?' + params.toString(), { headers: { 'Accept': 'application/json' } });
                var data = await res.json();

                rebuildOptions(data);
            }

            cityInput.addEventListener('input', function () {
                clearTimeout(debounce);
                debounce = setTimeout(function () {
                    fetchOptions().catch(function () {
                        // Keep existing UI if fetch fails.
                    });
                }, 450);
            });
        })();
    </script>
@endpush
@endsection
