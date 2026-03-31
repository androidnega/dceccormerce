@php
    $columns = collect([
        ['title' => 'New Arrivals', 'items' => $showcaseNewArrivals],
        ['title' => 'Featured Products', 'items' => $showcaseFeatured],
        ['title' => 'Best Sellers', 'items' => $showcaseBestSellers],
    ])->filter(fn ($c) => $c['items']->isNotEmpty());

    $colCount = $columns->count();
    $gridClass = match (true) {
        $colCount === 1 => 'grid-cols-1',
        $colCount === 2 => 'grid-cols-1 md:grid-cols-2',
        default => 'grid-cols-1 md:grid-cols-3',
    };
@endphp

@if ($columns->isNotEmpty())
    <section class="border-b border-neutral-100 bg-white py-10 md:py-14" aria-labelledby="home-triple-showcase-heading">
        <div class="store-box">
            <h2 id="home-triple-showcase-heading" class="sr-only">New arrivals, featured products, and best sellers</h2>
            <div class="grid gap-10 {{ $gridClass }} md:gap-8 lg:gap-12">
                @foreach ($columns as $column)
                    <div class="flex min-w-0 flex-col">
                        <h3 class="border-b border-neutral-200 pb-3 text-lg font-bold tracking-tight text-neutral-900">
                            {{ $column['title'] }}
                        </h3>
                        <ul class="flex flex-col" role="list">
                            @foreach ($column['items'] as $product)
                                @php
                                    $thumb = $product->images->first();
                                @endphp
                                <li class="border-b border-neutral-100 last:border-b-0">
                                    <a
                                        href="{{ route('products.show', $product) }}"
                                        class="group flex items-center gap-4 py-4 transition hover:bg-neutral-50/80"
                                    >
                                        <span class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-neutral-100 bg-neutral-50 ring-1 ring-black/[0.03]">
                                            @if ($thumb)
                                                <img
                                                    src="{{ $thumb->url() }}"
                                                    alt=""
                                                    class="h-full w-full object-contain object-center p-1.5 transition duration-300 group-hover:scale-[1.03]"
                                                    loading="lazy"
                                                >
                                            @else
                                                <span class="text-[10px] text-neutral-400">No image</span>
                                            @endif
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="line-clamp-2 text-[15px] font-medium leading-snug text-neutral-900 group-hover:text-brand-blue">
                                                {{ $product->name }}
                                            </span>
                                            <span class="mt-1 block text-base font-semibold tabular-nums text-red-600">
                                                {{ format_ghs($product->price) }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
