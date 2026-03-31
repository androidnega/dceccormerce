@php
    $products = $homepageSectionProducts[$section->id] ?? collect();
@endphp
@if ($products->isNotEmpty())
    <section class="border-b border-[#ebe7df] bg-[#faf8f5]/60 py-14 md:py-16" aria-labelledby="grid-{{ $section->id }}">
        <div class="store-box">
            @if (filled($section->title))
                <div class="mb-10 text-center md:mb-12">
                    <h2 id="grid-{{ $section->id }}" class="text-2xl font-semibold tracking-tight text-neutral-900 md:text-3xl">{{ $section->title }}</h2>
                    @if (filled($section->subtitle))
                        <p class="mt-2 text-sm text-neutral-500 md:text-base">{{ $section->subtitle }}</p>
                    @endif
                </div>
            @endif
            <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4 lg:gap-8">
                @foreach ($products as $product)
                    @include('products.partials.product-card', [
                        'product' => $product,
                        'productDisplay' => $productDisplay,
                        'showCategory' => true,
                    ])
                @endforeach
            </div>
        </div>
    </section>
@endif
