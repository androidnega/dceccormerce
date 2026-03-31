@php
    $variant = $section->config['variant'] ?? 'strip';
    $img = $section->imageUrl();
@endphp
@if ($variant === 'banner' && $img)
    <section class="border-b border-[#e8e4dc] bg-[#faf8f5]" aria-label="{{ $section->title ?? 'Promo' }}">
        <div class="store-box py-4 md:py-6">
            @if ($section->resolvedLink())
                <a href="{{ $section->resolvedLink() }}" class="block overflow-hidden rounded-3xl ring-1 ring-black/[0.06] transition hover:opacity-95">
                    <img src="{{ $img }}" alt="" class="h-auto max-h-[min(420px,50vh)] w-full object-cover object-center" loading="lazy">
                </a>
            @else
                <div class="overflow-hidden rounded-3xl ring-1 ring-black/[0.06]">
                    <img src="{{ $img }}" alt="" class="h-auto max-h-[min(420px,50vh)] w-full object-cover object-center" loading="lazy">
                </div>
            @endif
        </div>
    </section>
@else
    @php $href = $section->resolvedLink(); @endphp
    <div class="border-b border-neutral-800/20 bg-neutral-950 text-white">
        @if ($href)
            <a href="{{ $href }}" class="store-box block py-3 text-center transition hover:bg-neutral-900 md:py-3.5">
        @else
            <div class="store-box py-3 text-center md:py-3.5">
        @endif
                <p class="text-[13px] font-medium tracking-wide text-white/95 md:text-sm">
                    {{ $section->title ?? 'Announcement' }}
                </p>
                @if (filled($section->subtitle))
                    <p class="mt-0.5 text-xs text-white/70 md:text-[13px]">{{ $section->subtitle }}</p>
                @endif
        @if ($href)
            </a>
        @else
            </div>
        @endif
    </div>
@endif
