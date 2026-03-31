@php
    $stripMode = $stripMode ?? false;
    $inverted = $inverted ?? false;
    $yt = $promo->youtubeEmbedSrc();
    $vm = $promo->vimeoEmbedSrc();
    $imgUrl = $promo->heroImageUrl();
    $directVid = $promo->isDirectVideoFile() ? $promo->videoPageUrl() : null;

    $labelClass = $inverted
        ? 'text-amber-300/95'
        : match ($promo->type) {
            \App\Models\Promo::TYPE_FREE_DELIVERY => 'text-emerald-800',
            \App\Models\Promo::TYPE_DISCOUNT => 'text-violet-900',
            default => 'text-[#00479a]',
        };

    $titleClass = $inverted ? 'text-white' : 'text-neutral-950';
    $bodyClass = $inverted ? 'text-slate-200' : 'text-neutral-700';
    $linkPrimary = $inverted
        ? 'inline-flex items-center gap-2 rounded-full bg-amber-400 px-4 py-2 text-sm font-bold text-slate-900 transition hover:bg-amber-300'
        : 'inline-flex items-center gap-2 rounded-full bg-[#0057b8] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#00479a]';
    $linkSecondary = $inverted
        ? 'text-sm font-semibold text-amber-200 underline decoration-amber-400/80 underline-offset-2 hover:text-white'
        : 'text-sm font-semibold text-[#00479a] underline decoration-[#cce0f7] underline-offset-2 hover:text-[#003366]';
@endphp
<article @class([
    'flex overflow-hidden rounded-2xl border transition',
    'flex-col' => ! $stripMode,
    'min-w-[min(100%,22rem)] shrink-0 flex-col sm:min-w-0 sm:flex-1 sm:flex-row sm:items-stretch' => $stripMode,
    $inverted
        ? 'border-white/15 bg-white/[0.07] shadow-lg shadow-black/20 backdrop-blur-sm hover:border-white/25'
        : 'border-neutral-200 bg-white shadow-md ring-1 ring-black/[0.04] hover:shadow-lg',
])>
    @php
        $mediaWrap = $stripMode
            ? 'relative w-full shrink-0 bg-black sm:w-[min(42%,220px)] sm:max-w-[240px]'
            : 'relative w-full bg-black';
        $aspectMedia = $stripMode ? 'aspect-[16/10] sm:aspect-auto sm:h-full sm:min-h-[140px]' : 'aspect-video';
        $aspectImg = $stripMode ? 'aspect-[16/10] sm:aspect-auto sm:h-full sm:min-h-[140px]' : 'aspect-[16/10]';
    @endphp

    @if ($promo->hasHeroVideo())
        <div class="{{ $mediaWrap }} {{ $aspectMedia }}">
            @if ($yt)
                <iframe
                    class="absolute inset-0 h-full w-full"
                    src="{{ $yt }}"
                    title="{{ $promo->title }}"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            @elseif ($vm)
                <iframe
                    class="absolute inset-0 h-full w-full"
                    src="{{ $vm }}"
                    title="{{ $promo->title }}"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            @elseif ($directVid)
                <video class="h-full w-full object-cover" controls playsinline preload="metadata" src="{{ $directVid }}"></video>
            @else
                <div class="flex h-full min-h-[120px] items-center justify-center p-4 text-center text-xs text-white/80">Add a video URL in admin (YouTube, Vimeo, or .mp4).</div>
            @endif
        </div>
    @elseif ($imgUrl)
        <div class="{{ $mediaWrap }} {{ $aspectImg }} bg-neutral-200">
            <img src="{{ $imgUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
        </div>
    @else
        <div @class([
            'flex items-center justify-center px-4 text-center',
            $stripMode ? 'min-h-[120px] w-full sm:min-h-[140px] sm:w-[min(42%,220px)]' : 'aspect-[16/10]',
            'bg-gradient-to-br from-[#0057b8] to-[#001a3d] text-white' => $promo->type === \App\Models\Promo::TYPE_BANNER && ! $inverted,
            'bg-gradient-to-br from-emerald-700 to-teal-950 text-white' => $promo->type === \App\Models\Promo::TYPE_FREE_DELIVERY && ! $inverted,
            'bg-gradient-to-br from-violet-700 to-indigo-950 text-white' => $promo->type === \App\Models\Promo::TYPE_DISCOUNT && ! $inverted,
            'bg-gradient-to-br from-slate-700 to-slate-900 text-white' => $inverted,
        ])>
            @if ($promo->type === \App\Models\Promo::TYPE_FREE_DELIVERY)
                <i class="fa-solid fa-truck text-4xl text-white drop-shadow-sm" aria-hidden="true"></i>
            @elseif ($promo->type === \App\Models\Promo::TYPE_DISCOUNT)
                <span class="text-4xl font-black text-white drop-shadow-sm" aria-hidden="true">%</span>
            @else
                <i class="fa-solid fa-bolt text-4xl text-white drop-shadow-sm" aria-hidden="true"></i>
            @endif
        </div>
    @endif

    <div class="flex min-w-0 flex-1 flex-col justify-center p-5 sm:p-6">
        @if ($promo->type === \App\Models\Promo::TYPE_DISCOUNT && is_numeric(trim($promo->value)))
            <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] {{ $labelClass }}">Cart savings</p>
        @elseif ($promo->type === \App\Models\Promo::TYPE_FREE_DELIVERY)
            <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] {{ $labelClass }}">Delivery</p>
        @elseif ($promo->type === \App\Models\Promo::TYPE_BANNER)
            <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] {{ $labelClass }}">Featured</p>
        @endif

        <h3 class="mt-2 text-lg font-bold leading-snug {{ $titleClass }} sm:text-xl">{{ $promo->title }}</h3>

        @if ($promo->type === \App\Models\Promo::TYPE_DISCOUNT && is_numeric(trim($promo->value)))
            <p class="mt-2 text-sm leading-relaxed {{ $bodyClass }}">
                <span class="font-semibold {{ $inverted ? 'text-amber-200' : 'text-neutral-900' }}">{{ trim($promo->value) }}% off</span>
                your cart subtotal — applied at checkout.
            </p>
        @elseif ($promo->type === \App\Models\Promo::TYPE_FREE_DELIVERY)
            <p class="mt-2 text-sm leading-relaxed {{ $bodyClass }}">We’ll highlight this on checkout so shoppers see your delivery perk.</p>
        @endif

        <div class="mt-5 flex flex-wrap items-center gap-3">
            @if ($promo->type === \App\Models\Promo::TYPE_BANNER)
                <a href="{{ $promo->bannerLinkHref() }}" class="{{ $linkPrimary }}">
                    Shop this offer
                    <i class="fa-solid fa-arrow-right text-xs opacity-90" aria-hidden="true"></i>
                </a>
            @else
                <a href="{{ route('products.index') }}" class="{{ $linkPrimary }}">Browse products</a>
            @endif
        </div>
    </div>
</article>
