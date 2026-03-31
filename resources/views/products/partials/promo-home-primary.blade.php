@php
    $homepagePromosPrimary = $homepagePromosPrimary ?? collect();
@endphp
@if ($homepagePromosPrimary->isNotEmpty())
    <section class="border-y border-neutral-200 bg-gradient-to-b from-neutral-100/90 to-white py-12 md:py-16" aria-labelledby="live-offers-heading">
        <div class="store-box">
            <div class="mb-8 text-center md:mb-10">
                <h2 id="live-offers-heading" class="text-2xl font-bold tracking-tight text-neutral-950 md:text-3xl">More offers</h2>
                <p class="mt-2 text-base text-neutral-700">Deals and highlights — slot: grid below category banners.</p>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($homepagePromosPrimary as $promo)
                    @include('products.partials.promo-home-card', ['promo' => $promo, 'stripMode' => false, 'inverted' => false])
                @endforeach
            </div>
        </div>
    </section>
@endif
