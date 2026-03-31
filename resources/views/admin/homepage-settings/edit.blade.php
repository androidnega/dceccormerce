@extends('layouts.dashboard')

@section('title', 'Homepage — Admin')
@section('heading', 'Homepage')
@section('subheading', 'Layout, promos, and storefront content blocks.')

@section('content')
    <p class="mb-8 max-w-2xl text-sm text-zinc-600">
        Hero slideshow: <a href="{{ route('dashboard.hero-slides.index') }}" class="font-medium text-zinc-900 underline">Hero slides</a>.
        Promo cards below the hero are configured here.
    </p>

    <form action="{{ route('dashboard.homepage-settings.update') }}" method="post" enctype="multipart/form-data" class="admin-form max-w-6xl space-y-10">
        @csrf
        @method('PUT')
        <fieldset class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03]">
            <legend class="flex items-center gap-2 text-base font-semibold text-neutral-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-palette" aria-hidden="true"></i></span>
                Homepage style
            </legend>
            <label class="flex cursor-pointer gap-3 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm has-[:checked]:border-primary-600 has-[:checked]:ring-1 has-[:checked]:ring-primary-600">
                <input type="radio" name="homepage_layout" value="{{ \App\Models\HomepageSetting::LAYOUT_CAROUSEL }}" class="mt-1 text-primary-600" @checked(old('homepage_layout', $settings->homepage_layout) === \App\Models\HomepageSetting::LAYOUT_CAROUSEL)>
                <div>
                    <span class="font-medium text-neutral-900">Full-width carousel</span>
                    <p class="mt-1 text-sm text-neutral-500">Standard hero slideshow edge-to-edge below the header, then promos and storefront blocks.</p>
                </div>
            </label>
            <label class="flex cursor-pointer gap-3 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm has-[:checked]:border-primary-600 has-[:checked]:ring-1 has-[:checked]:ring-primary-600">
                <input type="radio" name="homepage_layout" value="{{ \App\Models\HomepageSetting::LAYOUT_SIDEBAR }}" class="mt-1 text-primary-600" @checked(old('homepage_layout', $settings->homepage_layout) === \App\Models\HomepageSetting::LAYOUT_SIDEBAR)>
                <div>
                    <span class="font-medium text-neutral-900">Categories + hero</span>
                    <p class="mt-1 text-sm text-neutral-500">Categories sidebar (or mobile strip) beside the hero slideshow — good for category-first browsing.</p>
                </div>
            </label>
            <label class="flex cursor-pointer gap-3 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm has-[:checked]:border-primary-600 has-[:checked]:ring-1 has-[:checked]:ring-primary-600">
                <input type="radio" name="homepage_layout" value="{{ \App\Models\HomepageSetting::LAYOUT_STACKED_CARDS }}" class="mt-1 text-primary-600" @checked(old('homepage_layout', $settings->homepage_layout) === \App\Models\HomepageSetting::LAYOUT_STACKED_CARDS)>
                <div>
                    <span class="font-medium text-neutral-900">Stacked Card Carousel (Premium)</span>
                    <p class="mt-1 text-sm text-neutral-500">Overlapping white product cards with a large center slide — ideal for flagship showcases. Set the stage color below.</p>
                </div>
            </label>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5">
                <div class="min-w-0 rounded-xl border border-neutral-200 bg-neutral-50/90 p-4">
                    <label class="block text-sm font-medium text-neutral-900" for="stacked_cards_stage_bg_hex">Stacked carousel — stage background</label>
                    <p class="mt-1 text-xs leading-snug text-neutral-500">Used only when “Stacked Card Carousel” is selected. Nav arrows and dots adjust automatically for light or dark colors.</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <input
                            type="color"
                            id="stacked_cards_stage_bg_picker"
                            value="{{ old('stacked_cards_stage_bg_hex', $settings->stackedCardsStageBgHex()) }}"
                            class="h-10 w-14 shrink-0 cursor-pointer rounded-lg border border-neutral-300 bg-white p-0.5 shadow-sm"
                            aria-label="Pick stage background color"
                        >
                        <input
                            type="text"
                            name="stacked_cards_stage_bg_hex"
                            id="stacked_cards_stage_bg_hex"
                            value="{{ old('stacked_cards_stage_bg_hex', $settings->stackedCardsStageBgHex()) }}"
                            required
                            pattern="#[0-9A-Fa-f]{6}"
                            placeholder="#0a0a0a"
                            class="min-w-0 flex-1 rounded-lg border border-neutral-200 bg-white px-3 py-2 font-mono text-sm text-neutral-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 md:max-w-full"
                        >
                    </div>
                    @error('stacked_cards_stage_bg_hex')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="min-w-0 rounded-xl border border-neutral-200 bg-neutral-50/90 p-4">
                    <p class="text-sm font-medium text-neutral-900">Full-width carousel — slide colors</p>
                    <p class="mt-1 text-xs leading-snug text-neutral-500">Used when “Full-width carousel” is selected. Sets the hero strip behind the slideshow (headline, subline, and dot accent).</p>
                    <div class="mt-4 space-y-4">
                        <div class="min-w-0">
                            <label class="block text-xs font-medium text-neutral-700" for="hero_fullwidth_bg_hex">Hero background</label>
                            <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                <input
                                    type="color"
                                    id="hero_fullwidth_bg_picker"
                                    value="{{ old('hero_fullwidth_bg_hex', $settings->heroFullwidthBgHex()) }}"
                                    class="h-10 w-14 shrink-0 cursor-pointer rounded-lg border border-neutral-300 bg-white p-0.5 shadow-sm"
                                    aria-label="Pick full-width hero background"
                                    data-fw-bg-picker
                                >
                                <input
                                    type="text"
                                    name="hero_fullwidth_bg_hex"
                                    id="hero_fullwidth_bg_hex"
                                    value="{{ old('hero_fullwidth_bg_hex', $settings->heroFullwidthBgHex()) }}"
                                    required
                                    pattern="#[0-9A-Fa-f]{6}"
                                    class="min-w-0 flex-1 rounded-lg border border-neutral-200 bg-white px-3 py-2 font-mono text-sm text-neutral-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    data-fw-bg-hex
                                >
                            </div>
                            @error('hero_fullwidth_bg_hex')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="min-w-0">
                            <label class="block text-xs font-medium text-neutral-700" for="hero_fullwidth_text_hex">Slide text</label>
                            <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                <input
                                    type="color"
                                    id="hero_fullwidth_text_picker"
                                    value="{{ old('hero_fullwidth_text_hex', $settings->heroFullwidthTextHex()) }}"
                                    class="h-10 w-14 shrink-0 cursor-pointer rounded-lg border border-neutral-300 bg-white p-0.5 shadow-sm"
                                    aria-label="Pick full-width hero text color"
                                    data-fw-text-picker
                                >
                                <input
                                    type="text"
                                    name="hero_fullwidth_text_hex"
                                    id="hero_fullwidth_text_hex"
                                    value="{{ old('hero_fullwidth_text_hex', $settings->heroFullwidthTextHex()) }}"
                                    required
                                    pattern="#[0-9A-Fa-f]{6}"
                                    class="min-w-0 flex-1 rounded-lg border border-neutral-200 bg-white px-3 py-2 font-mono text-sm text-neutral-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    data-fw-text-hex
                                >
                            </div>
                            <p class="mt-1.5 text-[11px] leading-snug text-neutral-500">Subline uses this color at ~78% opacity. CTA button stays store blue.</p>
                            @error('hero_fullwidth_text_hex')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03]">
            <legend class="flex items-center gap-2 text-base font-semibold text-neutral-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                Demo sidebar — nine categories
            </legend>
            <p class="mt-2 text-sm text-neutral-600">Choose one category per row (1–9). <strong class="text-neutral-800">Each category can only appear once</strong> — picking a category in one row removes it from the other dropdowns. Leave a slot as &quot;None&quot; for a blank row. If every slot is empty, the first nine categories by name are used until you save.</p>
            @error('sidebar_category_ids')
                <p class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $message }}</p>
            @enderror
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @for ($i = 0; $i < 9; $i++)
                    @php
                        $selId = old("sidebar_category_ids.$i", $sidebarPadded[$i] ?? '');
                        $selCat = $selId !== '' && $selId !== null ? $categories->firstWhere('id', (int) $selId) : null;
                    @endphp
                    <label class="block rounded-xl border border-neutral-100 bg-neutral-50/80 p-3 transition hover:border-indigo-200 hover:bg-white">
                        <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-neutral-500">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-indigo-600 shadow-sm ring-1 ring-neutral-200/80">
                                <i class="{{ $selCat ? category_fa_classes($selCat->slug, $selCat->name) : 'fa-solid fa-minus' }} js-row-icon" data-row-icon="{{ $i }}" aria-hidden="true"></i>
                            </span>
                            Row {{ $i + 1 }}
                        </span>
                        <select
                            name="sidebar_category_ids[{{ $i }}]"
                            class="js-sidebar-slot mt-2 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            data-row-index="{{ $i }}"
                            data-icon-target="[data-row-icon={{ $i }}]"
                        >
                            <option value="">— None —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $selId === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endfor
            </div>
        </fieldset>

        <fieldset class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03]">
            <legend class="flex w-full min-w-0 flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                <span class="flex items-center gap-2 text-base font-semibold text-neutral-900">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600"><i class="fa-solid fa-rectangle-ad" aria-hidden="true"></i></span>
                    <span class="leading-tight">Promo banners <span class="font-normal text-neutral-500">· three cards under hero</span></span>
                </span>
            </legend>
            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-neutral-600">
                Shown below the hero on <strong class="font-medium text-neutral-800">full-width</strong> and <strong class="font-medium text-neutral-800">stacked-card</strong> layouts only (hidden on categories + hero). Use a wide image per card, or keep assets in <code class="rounded-md bg-neutral-100 px-1.5 py-0.5 font-mono text-xs text-neutral-800">public/images/</code>.
            </p>

            <div class="mt-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-3 xl:gap-5">
                @foreach (range(0, 2) as $pi)
                    @php
                        $slot = $promoSlots[$pi] ?? [];
                        $previewUrl = $settings->resolvePromoImageUrl($slot['image_path'] ?? '');
                        $bgHex = old("promo_banners.$pi.background_hex", $slot['background_hex'] ?? '#d8cdc0');
                        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $bgHex)) {
                            $bgHex = '#d8cdc0';
                        }
                    @endphp
                    @include('admin.homepage-settings._promo-banner-slot', [
                        'pi' => $pi,
                        'slot' => $slot,
                        'previewUrl' => $previewUrl,
                        'bgHex' => $bgHex,
                    ])
                @endforeach
            </div>
        </fieldset>

        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-500/25 transition hover:bg-indigo-500"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Save homepage settings</button>
    </form>

    <p class="mt-10 text-sm text-neutral-500">
        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center gap-2 font-medium text-indigo-700 hover:text-indigo-900"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Admin home</a>
    </p>

    @php
        $sidebarCategoriesPayload = $categories->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'icon' => category_fa_classes($c->slug, $c->name),
            ];
        })->values();
    @endphp

    @push('scripts')
        <script>
            window.__sidebarCategoriesForSlots = @json($sidebarCategoriesPayload);
        </script>
        <script>
            (function () {
                var cats = window.__sidebarCategoriesForSlots || [];
                var iconDefault = 'fa-solid fa-minus';
                var selects = document.querySelectorAll('select.js-sidebar-slot');
                if (!selects.length) return;

                function iconForCategoryId(id) {
                    if (!id) return iconDefault;
                    var s = String(id);
                    for (var i = 0; i < cats.length; i++) {
                        if (String(cats[i].id) === s) return cats[i].icon || 'fa-solid fa-tag';
                    }
                    return 'fa-solid fa-tag';
                }

                function syncRowIcons() {
                    selects.forEach(function (sel) {
                        var row = sel.getAttribute('data-row-index');
                        var iconEl = document.querySelector('[data-row-icon="' + row + '"]');
                        if (!iconEl) return;
                        iconEl.setAttribute('class', iconForCategoryId(sel.value) + ' js-row-icon');
                    });
                }

                function rebuildOptions() {
                    var values = Array.prototype.map.call(selects, function (s) {
                        return s.value || '';
                    });
                    var seen = {};
                    values.forEach(function (v, idx) {
                        if (!v) return;
                        if (seen[v]) values[idx] = '';
                        else seen[v] = true;
                    });
                    Array.prototype.forEach.call(selects, function (sel, i) {
                        var keep = values[i] || '';
                        var usedElsewhere = {};
                        values.forEach(function (v, j) {
                            if (j !== i && v) usedElsewhere[v] = true;
                        });
                        if (keep && usedElsewhere[keep]) {
                            keep = '';
                            values[i] = '';
                        }
                        var frag = document.createDocumentFragment();
                        var o0 = document.createElement('option');
                        o0.value = '';
                        o0.textContent = '— None —';
                        frag.appendChild(o0);
                        cats.forEach(function (cat) {
                            var id = String(cat.id);
                            if (usedElsewhere[id] && id !== (values[i] || '')) return;
                            var opt = document.createElement('option');
                            opt.value = id;
                            opt.textContent = cat.name;
                            if (id === (values[i] || '')) opt.selected = true;
                            frag.appendChild(opt);
                        });
                        sel.innerHTML = '';
                        sel.appendChild(frag);
                        if (keep) sel.value = keep;
                    });
                    syncRowIcons();
                }

                Array.prototype.forEach.call(selects, function (sel) {
                    sel.addEventListener('change', rebuildOptions);
                });
                rebuildOptions();
            })();
        </script>
        <script>
            (function () {
                var picker = document.getElementById('stacked_cards_stage_bg_picker');
                var hexInput = document.getElementById('stacked_cards_stage_bg_hex');
                if (!picker || !hexInput) return;
                function normalizeHex(v) {
                    v = (v || '').trim();
                    if (/^#[0-9A-Fa-f]{6}$/.test(v)) return v.toLowerCase();
                    return null;
                }
                picker.addEventListener('input', function () {
                    hexInput.value = picker.value;
                });
                hexInput.addEventListener('change', function () {
                    var n = normalizeHex(hexInput.value);
                    if (n) picker.value = n;
                });
                hexInput.addEventListener('blur', function () {
                    var n = normalizeHex(hexInput.value);
                    if (n) {
                        hexInput.value = n;
                        picker.value = n;
                    }
                });
            })();
        </script>
        <script>
            (function () {
                function normalizeHex(v) {
                    v = (v || '').trim();
                    if (/^#[0-9A-Fa-f]{6}$/.test(v)) return v.toLowerCase();
                    return null;
                }
                function bindPair(pickerSel, hexSel) {
                    var picker = document.querySelector(pickerSel);
                    var hexInput = document.querySelector(hexSel);
                    if (!picker || !hexInput) return;
                    picker.addEventListener('input', function () { hexInput.value = picker.value; });
                    hexInput.addEventListener('change', function () {
                        var n = normalizeHex(hexInput.value);
                        if (n) picker.value = n;
                    });
                    hexInput.addEventListener('blur', function () {
                        var n = normalizeHex(hexInput.value);
                        if (n) { hexInput.value = n; picker.value = n; }
                    });
                }
                bindPair('[data-fw-bg-picker]', '[data-fw-bg-hex]');
                bindPair('[data-fw-text-picker]', '[data-fw-text-hex]');
            })();
        </script>
        <script>
            (function () {
                function normalizeHex(v) {
                    v = (v || '').trim();
                    if (/^#[0-9A-Fa-f]{6}$/.test(v)) return v.toLowerCase();
                    return null;
                }
                document.querySelectorAll('[data-promo-bg-picker]').forEach(function (picker) {
                    var pi = picker.getAttribute('data-promo-bg-picker');
                    var hexInput = document.querySelector('[data-promo-bg-hex="' + pi + '"]');
                    if (!hexInput) return;
                    picker.addEventListener('input', function () {
                        hexInput.value = picker.value;
                    });
                    hexInput.addEventListener('change', function () {
                        var n = normalizeHex(hexInput.value);
                        if (n) picker.value = n;
                    });
                    hexInput.addEventListener('blur', function () {
                        var n = normalizeHex(hexInput.value);
                        if (n) {
                            hexInput.value = n;
                            picker.value = n;
                        }
                    });
                });
            })();
        </script>
    @endpush
@endsection
