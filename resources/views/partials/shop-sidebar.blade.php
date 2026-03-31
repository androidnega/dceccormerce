@php
    $isDrawer = $isDrawer ?? false;
    $telRaw = trim((string) config('store.phone_tel'));
    if ($telRaw === '') {
        $telRaw = preg_replace('/\D+/', '', (string) config('store.phone'));
    }
@endphp
<nav class="{{ $isDrawer ? 'p-6' : 'sticky top-28 rounded-3xl border border-slate-200/90 bg-white/95 p-6 shadow-xl shadow-slate-200/40 ring-1 ring-slate-100 backdrop-blur-md' }}" aria-label="Shop by category">
    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Phones &amp; devices</p>
    <ul class="mt-4 space-y-0.5">
        <li>
            <a
                href="{{ route('products.index', array_filter(['search' => ($search ?? '') ?: null, 'category' => null])) }}"
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ ($categorySlug ?? '') === '' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-700' }}"
            >
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-[15px] {{ ($categorySlug ?? '') === '' ? '' : 'bg-slate-100 text-slate-500' }}" aria-hidden="true"><i class="fa-solid fa-border-all {{ ($categorySlug ?? '') === '' ? 'text-white' : '' }}"></i></span>
                All products
            </a>
        </li>
        @foreach (($categories ?? collect()) as $cat)
            <li>
                <a
                    href="{{ route('products.index', array_filter(['search' => ($search ?? '') ?: null, 'category' => $cat->slug])) }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ ($categorySlug ?? '') === $cat->slug ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-700' }}"
                >
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[15px] text-indigo-600 {{ ($categorySlug ?? '') === $cat->slug ? 'bg-white text-indigo-600 ring-1 ring-indigo-100' : '' }}" aria-hidden="true"><i class="{{ category_fa_classes($cat->slug, $cat->name) }}"></i></span>
                    <span class="line-clamp-2">{{ $cat->name }}</span>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="mt-6 border-t border-slate-100 pt-6">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Need help?</p>
        <p class="mt-2 text-xs leading-relaxed text-slate-500">
            @if ($telRaw !== '')
                Call <a href="tel:{{ $telRaw }}" class="font-semibold text-indigo-600 hover:underline">{{ config('store.phone') }}</a>
            @else
                Call <span class="font-semibold text-slate-700">{{ config('store.phone') }}</span>
            @endif
        </p>
    </div>
</nav>
