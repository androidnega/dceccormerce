<style>
    :root {
        --store-accent: #0057b8;
        --store-accent-hover: #00479a;
        --store-yellow: #ffd700;
        --store-glow: rgba(0, 87, 184, 0.28);
        --store-box-max: 1280px;
    }
    /* Mobile: avoid horizontal scroll from full-bleed sections, wide flex rows, or long tokens. */
    html {
        -webkit-text-size-adjust: 100%;
        overflow-x: clip;
    }
    @supports not (overflow: clip) {
        html {
            overflow-x: hidden;
        }
    }
    body.store-theme {
        overflow-x: clip;
    }
    @supports not (overflow: clip) {
        body.store-theme {
            overflow-x: hidden;
        }
    }
    body.store-theme main {
        min-width: 0;
    }
    #store-scroll-top {
        right: max(1.25rem, env(safe-area-inset-right, 0px) + 0.25rem);
        bottom: max(1.5rem, env(safe-area-inset-bottom, 0px) + 0.5rem);
    }
    @media (min-width: 768px) {
        #store-scroll-top {
            right: max(2rem, env(safe-area-inset-right, 0px) + 0.25rem);
            bottom: max(2rem, env(safe-area-inset-bottom, 0px) + 0.5rem);
        }
    }
    /* Force flat colors (no gradients) across storefront UI. */
    [class*="bg-gradient-to-"] {
        background-image: none !important;
        background-color: var(--store-accent) !important;
    }
    [class*="from-"],
    [class*="via-"],
    [class*="to-"] {
        --tw-gradient-from: transparent !important;
        --tw-gradient-via: transparent !important;
        --tw-gradient-to: transparent !important;
    }
    [class*="bg-clip-text"] {
        -webkit-text-fill-color: currentColor !important;
        background-image: none !important;
    }
    .text-transparent {
        color: #0057b8 !important;
    }
    .text-indigo-50, .text-indigo-100, .text-indigo-200, .text-indigo-300, .text-indigo-400, .text-indigo-500, .text-indigo-600, .text-indigo-700, .text-indigo-800, .text-indigo-900,
    .text-cyan-200, .text-cyan-300, .text-cyan-400, .text-cyan-500, .text-cyan-600, .text-cyan-700 {
        color: #0057b8 !important;
    }
    .bg-indigo-50, .bg-indigo-100, .bg-indigo-200, .bg-indigo-500, .bg-indigo-600, .bg-indigo-700, .bg-indigo-800, .bg-violet-600, .bg-violet-700,
    .hover\:bg-indigo-50:hover, .hover\:bg-indigo-100:hover, .hover\:bg-indigo-500:hover, .hover\:bg-indigo-600:hover, .hover\:bg-indigo-700:hover,
    .hover\:bg-violet-500:hover, .hover\:bg-violet-600:hover {
        background-color: #0057b8 !important;
        color: #ffffff !important;
    }
    .border-indigo-100, .border-indigo-200, .border-indigo-300, .border-indigo-400, .border-indigo-500, .border-indigo-600, .border-indigo-700,
    .ring-indigo-100, .ring-indigo-200, .ring-indigo-300, .ring-indigo-400, .ring-indigo-500 {
        border-color: #0057b8 !important;
        --tw-ring-color: rgba(0, 87, 184, 0.25) !important;
    }
    .text-amber-400, .text-amber-500, .text-yellow-400, .text-yellow-500 {
        color: #ffd700 !important;
    }
    .bg-amber-400, .bg-amber-500, .bg-yellow-400, .bg-yellow-500 {
        background-color: #ffd700 !important;
        color: #1a1a1a !important;
    }
    /**
     * Standard boxed width — matches header / nav / footer inner column.
     */
    .store-box {
        width: 100%;
        max-width: var(--store-box-max);
        margin-left: auto;
        margin-right: auto;
        padding-left: 1rem;
        padding-right: 1rem;
        box-sizing: border-box;
    }
    @media (min-width: 640px) {
        .store-box {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
    }
    @media (min-width: 1024px) {
        .store-box {
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }
    .store-reveal {
        opacity: 0;
        transform: translateY(1.25rem);
        transition: opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1), transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .store-reveal-visible {
        opacity: 1;
        transform: translateY(0);
    }
    @keyframes store-float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-6px) rotate(1deg); }
    }
    @keyframes store-shimmer {
        100% { transform: translateX(100%); }
    }
    @keyframes store-pulse-ring {
        0%, 100% { box-shadow: 0 0 0 0 var(--store-glow); }
        50% { box-shadow: 0 0 0 8px transparent; }
    }
    .store-animate-float {
        animation: store-float 5s ease-in-out infinite;
    }
    .store-card-shine {
        position: relative;
        overflow: hidden;
    }
    .store-card-shine::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
        pointer-events: none;
    }
    .store-card-shine:hover::after {
        transform: translateX(100%);
    }
    .store-drawer-open {
        transform: translateX(0) !important;
    }
    .store-overlay-open {
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    .store-cart-pulse {
        animation: store-pulse-ring 2s ease-out infinite;
    }
    .store-input-focus:focus {
        outline: none;
        border-color: #111827;
        box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.12);
    }
    .store-checkout-section:focus-within {
        box-shadow: 0 0 0 1px rgba(17, 24, 39, 0.08);
    }
    .store-scrollbar-none {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .store-scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    #product-quick-view.store-modal-enter [data-pqv-panel] {
        animation: storeModalIn 0.28s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    @keyframes storeModalIn {
        from {
            opacity: 0;
            transform: scale(0.96) translateY(8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .group.store-mega-trigger[aria-expanded="true"] .store-mega-chevron {
        transform: rotate(180deg);
    }
</style>
