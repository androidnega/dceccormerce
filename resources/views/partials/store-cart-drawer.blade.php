<div
    id="store-cart-drawer"
    class="pointer-events-none fixed inset-0 z-[110] opacity-0 transition-opacity duration-300 ease-out"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-label="Shopping cart"
    data-cart-drawer
>
    <div
        class="absolute inset-0 bg-black/40 transition-opacity duration-300"
        data-cart-drawer-backdrop
    ></div>
    <aside
        class="absolute right-0 top-0 flex h-full w-full max-w-md translate-x-full flex-col bg-white shadow-2xl transition duration-300 ease-out will-change-transform"
        data-cart-drawer-panel
    >
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Cart</h2>
            <button
                type="button"
                class="store-btn-press flex h-10 w-10 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
                data-cart-drawer-close
                aria-label="Close cart"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="store-cart-drawer-body" class="flex-1 overflow-y-auto px-5 py-4">
            <div class="flex flex-col items-center gap-3 py-12 text-sm text-gray-500" data-cart-drawer-loading>
                <svg class="h-8 w-8 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading cart…</span>
            </div>
        </div>
    </aside>
</div>
