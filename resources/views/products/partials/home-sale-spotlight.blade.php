{{-- Compact 3-up product row (classic storefront sale strip) --}}
<section class="home-sale-spotlight border-b border-zinc-200/80 bg-[#fafafa] py-8 md:py-10" aria-labelledby="home-sale-spotlight-heading" data-home-sale-spotlight>
    <div class="store-box">
        <div class="mb-6 text-center md:mb-7">
            <h2 id="home-sale-spotlight-heading" class="text-lg font-semibold tracking-tight text-zinc-900 md:text-xl">On sale</h2>
            <p class="mt-1 text-sm text-zinc-500">Limited-time pricing in {{ config('store.currency_code') }}</p>
        </div>
        @php
            $spotlightCount = is_array($spotlightItems) ? count($spotlightItems) : 0;
            $gridCols = $spotlightCount === 1 ? 'sm:grid-cols-1' : ($spotlightCount === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-3');
        @endphp
        <div class="grid grid-cols-1 gap-4 {{ $gridCols }} sm:gap-5">
            @foreach ($spotlightItems as $item)
                @include('products.partials.card-home', [
                    'product' => $item['product'],
                    'variant' => 'sale-compact',
                    'saleStripImageUrl' => $item['imageUrl'] ?? null,
                ])
            @endforeach
        </div>
    </div>
</section>
@once
    @push('scripts')
        <style>
            .home-sale-spotlight [data-sale-spotlight-card].sale-spotlight-selected {
                border-color: #0057b8;
                box-shadow: 0 0 0 2px rgba(0, 87, 184, 0.22);
            }
        </style>
        <script>
            (function () {
                document.querySelectorAll('[data-home-sale-spotlight]').forEach(function (section) {
                    var cards = section.querySelectorAll('[data-sale-spotlight-card]');
                    if (!cards.length) return;
                    cards.forEach(function (card) {
                        card.addEventListener('click', function (e) {
                            if (e.target.closest('a[href]')) return;
                            var url = card.getAttribute('data-product-url');
                            if (url) {
                                window.location.href = url;
                                return;
                            }
                            e.preventDefault();
                            var was = card.classList.contains('sale-spotlight-selected');
                            cards.forEach(function (c) { c.classList.remove('sale-spotlight-selected'); });
                            if (!was) card.classList.add('sale-spotlight-selected');
                        });
                    });
                });
            })();
        </script>
    @endpush
@endonce
