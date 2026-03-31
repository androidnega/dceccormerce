@php $img = $section->imageUrl(); @endphp
<section class="border-b border-[#ebe7df] bg-white py-8 md:py-10" aria-labelledby="feat-promo-{{ $section->id }}">
    <div class="store-box">
        <div @class([
            'grid grid-cols-1 items-stretch gap-6 md:gap-8',
            'md:grid-cols-2' => $img,
        ])>
            <div @class(['order-2 flex min-w-0 flex-col justify-center md:order-1', 'mx-auto max-w-xl text-center' => ! $img])>
                @if (filled($section->title))
                    <h2 id="feat-promo-{{ $section->id }}" class="text-3xl font-semibold tracking-tight text-neutral-900 md:text-[2rem]">{{ $section->title }}</h2>
                @endif
                @if (filled($section->subtitle))
                    <p class="mt-4 text-base leading-relaxed text-neutral-500">{{ $section->subtitle }}</p>
                @endif
                @if ($section->resolvedLink())
                    <a href="{{ $section->resolvedLink() }}" @class([
                        'mt-8 inline-flex rounded-xl bg-neutral-900 px-8 py-3 text-sm font-semibold text-white transition hover:bg-neutral-800',
                        'mx-auto' => ! $img,
                    ])>
                        Explore
                    </a>
                @endif
            </div>
            @if ($img)
                {{--
                  Right column: full width of cell; square right edge (no rounding) so the image block
                  aligns with the .store-box inner right edge — same vertical line as header/footer content.
                --}}
                <div class="order-1 min-w-0 md:order-2">
                    @if ($section->resolvedLink())
                        <a href="{{ $section->resolvedLink() }}" class="block w-full max-w-none overflow-hidden rounded-3xl bg-white md:w-full md:rounded-l-3xl md:rounded-r-none">
                            <div class="flex h-[200px] w-full items-center justify-center bg-white sm:h-[220px] md:h-[260px] lg:h-[300px]">
                                <img src="{{ $img }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy">
                            </div>
                        </a>
                    @else
                        <div class="w-full max-w-none overflow-hidden rounded-3xl bg-white md:w-full md:rounded-l-3xl md:rounded-r-none">
                            <div class="flex h-[200px] w-full items-center justify-center bg-white sm:h-[220px] md:h-[260px] lg:h-[300px]">
                                <img src="{{ $img }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy">
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
