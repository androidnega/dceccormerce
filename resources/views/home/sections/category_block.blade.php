@php
    $banners = \App\Models\CategoryBanner::query()->activeOrdered()->limit(3)->get();
@endphp
@if ($banners->count() > 0)
    <section class="border-b border-[#ebe7df] bg-[#faf8f5] py-12 md:py-16" aria-labelledby="shop-by-category">
        <div class="store-box">
            <div class="mb-8 max-w-2xl">
                <h2 id="shop-by-category" class="text-2xl font-semibold tracking-tight text-neutral-900 md:text-3xl">Shop by category</h2>
                <p class="mt-2 text-sm text-neutral-500 md:text-base">Large visuals, calm layout — pick your lane.</p>
            </div>

            {{-- First two columns slightly narrower than equal split; third is the wide hero. --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-[0.8fr_0.8fr_1.55fr] md:items-stretch md:gap-5">
                @foreach ($banners as $b)
                    @php
                        $bg = $b->background_color ?: '#b06a70';
                        $tc = $b->text_color ?: '#ffffff';
                        $cta = $b->cta_text ?: 'Shop Now';
                        $link = trim((string) $b->link);
                        $imageUrl = $b->imageUrl();
                        $videoSrc = $b->videoSourceUrl();
                        $imgW = max(50, min(100, (int) ($b->image_width_percent ?? 90)));
                        $imgY = (int) ($b->image_offset_y ?? 0);
                        $imgPushDown = $imgY + 56;
                        $ytId = $b->youtubeVideoId();

                        $cardShell = 'group relative h-[400px] overflow-hidden rounded-[20px] md:h-[520px]';
                    @endphp

                    @if ($link !== '')
                        <a href="{{ $link }}" class="{{ $cardShell }} block" style="background-color: {{ $bg }}; color: {{ $tc }};">
                    @else
                        <div class="{{ $cardShell }}" style="background-color: {{ $bg }}; color: {{ $tc }};">
                    @endif

                        @if ($b->type === \App\Models\CategoryBanner::TYPE_VIDEO && $videoSrc)
                            <div class="absolute inset-0 overflow-hidden bg-black">
                                @if ($ytId)
                                    {{-- Oversized + centered so 16:9 video covers the full card (no empty bands). --}}
                                    <iframe
                                        class="pointer-events-none absolute left-1/2 top-1/2 min-h-full min-w-full -translate-x-1/2 -translate-y-1/2"
                                        style="width: 180%; height: 180%; max-width: none; max-height: none;"
                                        src="https://www.youtube-nocookie.com/embed/{{ $ytId }}?autoplay=1&mute=1&loop=1&playlist={{ $ytId }}&controls=0&modestbranding=1&rel=0&playsinline=1&iv_load_policy=3"
                                        title="Category video"
                                        loading="eager"
                                        allow="autoplay; encrypted-media; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                @else
                                    <video
                                        autoplay
                                        muted
                                        loop
                                        playsinline
                                        class="pointer-events-none absolute left-1/2 top-1/2 min-h-full min-w-full -translate-x-1/2 -translate-y-1/2 object-cover"
                                        style="width: 180%; height: 180%; max-width: none; max-height: none;"
                                    >
                                        <source src="{{ $videoSrc }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>
                            <div class="absolute inset-0 bg-black/30" aria-hidden="true"></div>

                            <div class="relative z-10 flex h-full flex-col items-center justify-end px-6 pb-9 text-center md:pb-10">
                                <h2 class="text-2xl font-semibold tracking-tight md:text-[1.65rem]">{{ $b->title }}</h2>
                                @if (filled($b->subtitle))
                                    <p class="mt-2 max-w-md text-sm font-normal opacity-95 md:text-[0.95rem]">{{ $b->subtitle }}</p>
                                @endif
                                <span class="mt-5 inline-flex rounded-full bg-white px-6 py-2.5 text-sm font-medium text-neutral-900">{{ $cta }}</span>
                            </div>
                        @else
                            <div class="relative z-20 flex flex-col items-center px-5 pt-5 text-center md:px-6 md:pt-6">
                                <h2 class="text-lg font-semibold leading-tight tracking-tight md:text-xl md:text-2xl">{{ $b->title }}</h2>
                                @if (filled($b->subtitle))
                                    <p class="mt-1.5 text-xs font-normal opacity-95 md:mt-2 md:text-sm">{{ $b->subtitle }}</p>
                                @endif
                                <span class="mt-2 inline-block text-xs font-normal underline underline-offset-[3px] decoration-white/90 md:mt-3 md:text-sm">{{ $cta }}</span>
                            </div>

                            @if ($imageUrl)
                                {{-- object-contain: whole image visible, scaled down to fit (no cropping). Smaller look = max-width % + padding. --}}
                                <div class="pointer-events-none absolute inset-x-0 bottom-0 top-[22%] z-0 overflow-hidden px-3 md:top-[18%] md:px-5">
                                    <div
                                        class="relative mx-auto flex h-full w-full min-h-0 items-end justify-center"
                                        style="transform: translateY({{ $imgPushDown }}px); max-width: {{ $imgW }}%;"
                                    >
                                        <img
                                            src="{{ $imageUrl }}"
                                            alt=""
                                            class="h-auto max-h-full w-auto max-w-full origin-bottom object-contain object-bottom transition-transform duration-[1100ms] ease-out group-hover:scale-[1.06] motion-reduce:transition-none motion-reduce:group-hover:scale-100"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    </div>
                                </div>
                            @endif
                        @endif

                    @if ($link !== '')
                        </a>
                    @else
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
