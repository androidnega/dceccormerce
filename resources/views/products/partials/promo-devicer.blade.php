@php
    $promos = ($homepageSettings ?? \App\Models\HomepageSetting::current())->promoBannersForView();
    $pair = array_slice($promos, 0, 2);
@endphp
<section class="border-b border-neutral-100 bg-white" aria-label="Promotions">
    <div class="store-box py-6 md:py-8">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5">
            @foreach ($pair as $promo)
                <a
                    href="{{ $promo['href'] }}"
                    class="group flex min-h-[268px] overflow-hidden rounded-2xl bg-[#ffeb3b] transition md:min-h-[300px] lg:min-h-[320px]"
                >
                    <div class="flex min-w-0 flex-1 flex-col justify-center px-6 py-6 text-left sm:px-8 sm:py-8">
                        @if (($promo['kicker'] ?? '') !== '')
                            <span class="text-[13px] font-medium text-neutral-500">{{ $promo['kicker'] }}</span>
                        @endif
                        <span class="mt-0.5 block text-[22px] font-extrabold leading-tight tracking-tight text-neutral-900 sm:text-[26px] md:text-[28px]">{{ $promo['headline'] }}</span>
                        <span class="mt-3 block text-[13px] font-medium text-neutral-500">Starting at</span>
                        <span class="mt-0.5 block text-[26px] font-bold tabular-nums leading-none text-[#0d9488] sm:text-[28px] md:text-[30px]">{{ $promo['price'] }}</span>
                    </div>
                    <div class="relative w-[54%] max-w-[380px] shrink-0 bg-[#ffeb3b] sm:w-[52%] lg:max-w-[440px]">
                        @if (($promo['image'] ?? '') !== '')
                            <img
                                src="{{ $promo['image'] }}"
                                alt="{{ $promo['alt'] }}"
                                class="absolute inset-0 h-full w-full object-contain object-bottom p-0.5 transition duration-300 group-hover:scale-[1.03] sm:p-1"
                                loading="lazy"
                                decoding="async"
                            >
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
