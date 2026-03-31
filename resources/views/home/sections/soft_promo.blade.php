<section class="border-b border-[#ebe7df] bg-white py-12 md:py-16" aria-labelledby="soft-{{ $section->id }}">
    <div class="store-box">
        <div class="rounded-3xl bg-[#f3f0ea] px-8 py-12 text-center ring-1 ring-[#e5dfd3] md:px-16 md:py-16">
            @if (filled($section->title))
                <h2 id="soft-{{ $section->id }}" class="text-2xl font-semibold tracking-tight text-neutral-900 md:text-3xl">{{ $section->title }}</h2>
            @endif
            @if (filled($section->subtitle))
                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-neutral-500 md:text-base">{{ $section->subtitle }}</p>
            @endif
            @if ($section->resolvedLink())
                <a href="{{ $section->resolvedLink() }}" class="mt-8 inline-flex text-sm font-semibold text-[#8b6914] underline decoration-[#d4b896] underline-offset-4 transition hover:text-[#6b5010]">
                    Shop the edit
                </a>
            @endif
        </div>
    </div>
</section>
