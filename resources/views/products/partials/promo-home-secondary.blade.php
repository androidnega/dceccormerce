@php
    $homepagePromosSecondary = $homepagePromosSecondary ?? collect();
@endphp
@if ($homepagePromosSecondary->isNotEmpty())
    <section class="relative border-y border-slate-800 bg-gradient-to-r from-slate-950 via-[#0c1e3d] to-slate-950 py-10 md:py-12" aria-labelledby="promo-spotlight-heading">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(255,215,0,0.08),_transparent_55%)]" aria-hidden="true"></div>
        <div class="store-box relative">
            <div class="text-center">
                <h2 id="promo-spotlight-heading" class="text-xs font-bold uppercase tracking-[0.28em] text-amber-300/95">Spotlight</h2>
                <p class="mt-2 text-base font-semibold text-white md:text-lg">Current perks — first thing shoppers see after the hero</p>
                <p class="mt-1 text-sm text-slate-300">Managed in <span class="font-medium text-slate-100">Dashboard → Promos</span> (slot: upper strip).</p>
            </div>
            <div class="mt-8 flex flex-col gap-5 sm:flex-row sm:flex-wrap sm:justify-center lg:gap-6">
                @foreach ($homepagePromosSecondary as $promo)
                    @include('products.partials.promo-home-card', ['promo' => $promo, 'stripMode' => true, 'inverted' => true])
                @endforeach
            </div>
        </div>
    </section>
@endif
