@php
    use App\Models\ProductImage;

    $recommendedProducts = $recommendedProducts ?? collect();
@endphp
@if (empty($lines))
    <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
        <p class="text-sm font-medium text-gray-600">Your cart is empty.</p>
        <a href="{{ route('products.index') }}" class="mt-4 rounded-xl bg-black px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 active:scale-[0.98]">Browse products</a>
    </div>
    @if ($recommendedProducts->isNotEmpty())
        <div class="border-t border-gray-100 px-4 pb-6 pt-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">You might also like</p>
            <ul class="mt-3 space-y-3" role="list">
                @foreach ($recommendedProducts as $rec)
                    @php $rimg = $rec->images->first(); @endphp
                    <li class="flex gap-3 rounded-xl border border-gray-100 bg-gray-50/80 p-2">
                        <a href="{{ route('products.show', $rec) }}" class="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-white">
                            @if ($rimg)
                                <img src="{{ $rimg->url() }}" alt="" class="h-full w-full object-contain p-0.5">
                            @else
                                <span class="flex h-full items-center justify-center text-[9px] text-gray-400">—</span>
                            @endif
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('products.show', $rec) }}" class="line-clamp-2 text-xs font-semibold text-gray-900 hover:underline">{{ $rec->name }}</a>
                            <p class="mt-0.5 text-xs tabular-nums text-gray-700">{{ format_ghs($rec->effectivePrice()) }}</p>
                            @if ($rec->stock > 0)
                                <form action="{{ route('cart.add', $rec->id) }}" method="post" class="store-add-cart-form mt-1.5">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="store-add-cart-btn border-0 bg-transparent p-0 text-[11px] font-semibold text-primary-700 underline-offset-2 hover:underline" data-add-label="Add to cart" data-added-label="Added">
                                        <span class="store-add-cart-label">Add to cart</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@else
    <ul class="divide-y divide-gray-100" role="list">
        @foreach ($lines as $productId => $line)
            <li class="flex gap-4 py-4 first:pt-0" data-cart-line="{{ $productId }}">
                <a href="{{ isset($line['slug']) ? route('products.show', $line['slug']) : route('products.index') }}" class="relative h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                    @if (! empty($line['image']))
                        <img src="{{ ProductImage::resolveUrl($line['image']) }}" alt="" class="h-full w-full object-contain p-1">
                    @else
                        <span class="flex h-full items-center justify-center text-[10px] text-gray-400">No img</span>
                    @endif
                </a>
                <div class="min-w-0 flex-1">
                    <p class="line-clamp-2 text-sm font-semibold text-gray-900">{{ $line['name'] }}</p>
                    <p class="mt-0.5 text-sm tabular-nums text-gray-600">
                        @if (! empty($line['list_price']))
                            <span class="text-gray-400 line-through">{{ format_ghs($line['list_price']) }}</span>
                            <span class="ml-1 font-medium text-gray-900">{{ format_ghs($line['price']) }}</span>
                        @else
                            {{ format_ghs($line['price']) }}
                        @endif
                        <span class="text-gray-500"> each</span>
                    </p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <form action="{{ route('cart.update', $productId) }}" method="post" class="inline-flex items-center gap-1" data-cart-update-form>
                            @csrf
                            <label class="sr-only" for="cart-qty-{{ $productId }}">Quantity</label>
                            <input
                                id="cart-qty-{{ $productId }}"
                                type="number"
                                name="quantity"
                                value="{{ $line['quantity'] }}"
                                min="1"
                                required
                                class="store-input-focus w-16 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-sm text-gray-900"
                            >
                            <button type="submit" class="rounded-lg border border-gray-200 px-2 py-1 text-xs font-medium text-gray-700 transition hover:bg-gray-50 active:scale-95">Update</button>
                        </form>
                        <form action="{{ route('cart.remove', $productId) }}" method="post" class="inline" data-cart-remove-form>
                            @csrf
                            <button type="submit" class="text-xs font-medium text-red-600 transition hover:text-red-800 active:scale-95">Remove</button>
                        </form>
                    </div>
                </div>
                <p class="shrink-0 text-sm font-semibold tabular-nums text-gray-900">{{ format_ghs($line['subtotal']) }}</p>
            </li>
        @endforeach
    </ul>
    <div class="mt-6 border-t border-gray-200 pt-4">
        <div class="rounded-xl bg-gray-50 px-4 py-3">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>Items in cart</span>
                <span>{{ collect($lines)->sum(fn ($l) => (int) ($l['quantity'] ?? 0)) }}</span>
            </div>
            <div class="mt-2 flex items-center justify-between text-lg font-bold text-gray-900">
                <span>Subtotal</span>
                <span class="tabular-nums" data-cart-drawer-subtotal>{{ format_ghs($total) }}</span>
            </div>
        </div>
        <a href="{{ route('checkout.index') }}" class="mt-4 flex w-full items-center justify-center rounded-xl bg-black py-3 text-sm font-semibold text-white transition hover:bg-gray-800 active:scale-[0.98]">
            Checkout
        </a>
        <a href="{{ route('products.index') }}" class="mt-3 flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-800 transition hover:bg-gray-50 active:scale-[0.98]">
            Continue shopping
        </a>
        @if (empty($hideCartPageLink))
            <a href="{{ route('cart.index') }}" class="mt-2 block w-full text-center text-sm text-gray-600 underline-offset-2 hover:underline">View full cart page</a>
        @endif
    </div>
    @if ($recommendedProducts->isNotEmpty())
        <div class="border-t border-gray-100 px-1 pb-2 pt-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recommended for you</p>
            <ul class="mt-3 space-y-3" role="list">
                @foreach ($recommendedProducts as $rec)
                    @php $rimg = $rec->images->first(); @endphp
                    <li class="flex gap-3 rounded-xl border border-gray-100 bg-gray-50/80 p-2">
                        <a href="{{ route('products.show', $rec) }}" class="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-white">
                            @if ($rimg)
                                <img src="{{ $rimg->url() }}" alt="" class="h-full w-full object-contain p-0.5">
                            @else
                                <span class="flex h-full items-center justify-center text-[9px] text-gray-400">—</span>
                            @endif
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('products.show', $rec) }}" class="line-clamp-2 text-xs font-semibold text-gray-900 hover:underline">{{ $rec->name }}</a>
                            <p class="mt-0.5 text-xs tabular-nums text-gray-700">{{ format_ghs($rec->effectivePrice()) }}</p>
                            @if ($rec->stock > 0)
                                <form action="{{ route('cart.add', $rec->id) }}" method="post" class="store-add-cart-form mt-1.5">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="store-add-cart-btn border-0 bg-transparent p-0 text-[11px] font-semibold text-primary-700 underline-offset-2 hover:underline" data-add-label="Add to cart" data-added-label="Added">
                                        <span class="store-add-cart-label">Add to cart</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endif
