@php
    $freeMin = config('store.free_shipping_min', 100);
@endphp
<section id="features" class="store-reveal border-b border-slate-200/80 bg-white py-8 md:py-10" aria-labelledby="features-heading">
    <h2 id="features-heading" class="sr-only">Why shop with us</h2>
    <div class="store-box">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
            <div class="flex gap-4">
                <span class="shrink-0 text-[#0f766e]" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9.75L12 3l9 6.75V21a1 1 0 01-1 1h-5.25v-6H9.25v6H4a1 1 0 01-1-1V9.75z"/>
                    </svg>
                </span>
                <div class="min-w-0 pt-0.5">
                    <p class="text-[15px] font-bold text-neutral-900">Free shipping</p>
                    <p class="mt-1 text-[13px] leading-snug text-neutral-500">Free for all orders over {{ format_ghs($freeMin) }}.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <span class="shrink-0 text-[#0f766e]" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 2L11 13"/>
                        <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                </span>
                <div class="min-w-0 pt-0.5">
                    <p class="text-[15px] font-bold text-neutral-900">Money comes back</p>
                    <p class="mt-1 text-[13px] leading-snug text-neutral-500">100% money back within 30 days.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <span class="shrink-0 text-[#0f766e]" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </span>
                <div class="min-w-0 pt-0.5">
                    <p class="text-[15px] font-bold text-neutral-900">24/7 Support</p>
                    <p class="mt-1 text-[13px] leading-snug text-neutral-500">Fast service — 24/7 support.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <span class="shrink-0 text-[#0f766e]" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 00-2.91-.09z"/>
                        <path d="M12 15l-3-3a22 22 0 012-3.95A12.88 12.88 0 0122 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 01-4 2z"/>
                        <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/>
                        <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                    </svg>
                </span>
                <div class="min-w-0 pt-0.5">
                    <p class="text-[15px] font-bold text-neutral-900">Fast delivery</p>
                    <p class="mt-1 text-[13px] leading-snug text-neutral-500">Best service for you.</p>
                </div>
            </div>
        </div>
    </div>
</section>
