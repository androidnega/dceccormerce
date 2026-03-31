{{-- Quick view (filled via JS from products.quick-view JSON) --}}
<div
    id="product-quick-view"
    class="fixed inset-0 z-[120] hidden items-center justify-center p-4 sm:p-6"
    data-pqv-root
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-label="Product quick view"
>
    <div class="absolute inset-0 bg-neutral-900/55 backdrop-blur-[2px] transition-opacity" data-pqv-backdrop tabindex="-1"></div>
    <div
        class="relative z-10 flex max-h-[min(90vh,880px)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-neutral-200/90 bg-white shadow-2xl shadow-neutral-900/20"
        data-pqv-panel
    >
        <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4 sm:px-6">
            <h2 class="pr-4 text-lg font-bold tracking-tight text-neutral-900 sm:text-xl">Quick view</h2>
            <button
                type="button"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900"
                data-pqv-close
                aria-label="Close"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="pqv-body" class="overflow-y-auto px-5 py-5 sm:px-6 sm:py-6">
            <div class="flex flex-col items-center gap-4 py-12 text-neutral-500" data-pqv-loading>
                <svg class="h-10 w-10 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-medium">Loading…</p>
            </div>
        </div>
    </div>
</div>
