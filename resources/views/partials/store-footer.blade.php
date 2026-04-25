@php
    $storeEmail = config('store.email');
    $footerPhone = config('store.phone');
    $footerPhoneTel = trim((string) config('store.phone_tel', ''));
    $telHref = $footerPhoneTel !== '' ? preg_replace('/\s+/', '', $footerPhoneTel) : preg_replace('/\D+/', '', $footerPhone);
    $footerCategories = ($categories ?? collect())->unique('name')->values()->take(4);
@endphp
<footer class="mt-auto">
    <div class="bg-[#0057b8] text-white">
        <div class="store-box py-10 sm:py-12">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-10 lg:gap-y-0 lg:items-start">
                {{-- Shop --}}
                <section aria-labelledby="footer-heading-shop" class="flex flex-col">
                    <h2 id="footer-heading-shop" class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#ffd700]">Shop</h2>
                    <nav class="mt-3" aria-label="Shop links">
                        <ul class="space-y-2 text-sm text-white/80">
                            <li><a href="{{ route('home') }}" class="transition hover:text-white">All phones</a></li>
                            @foreach ($footerCategories as $cat)
                                <li>
                                    <a href="{{ route('shop.category', $cat) }}" class="transition hover:text-white">{{ $cat->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </section>

                {{-- Support --}}
                <section id="about" aria-labelledby="footer-heading-support" class="flex flex-col">
                    <h2 id="footer-heading-support" class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#ffd700]">Support</h2>
                    <nav class="mt-3" aria-label="Support links">
                        <ul class="space-y-2 text-sm text-white/80">
                            <li><a href="{{ route('tracking.index') }}" class="transition hover:text-white">Track order</a></li>
                            <li><a href="{{ route('legal.refund-policy') }}" class="transition hover:text-white">Returns &amp; refunds</a></li>
                            <li><a href="{{ route('home') }}#features" class="transition hover:text-white">Shipping &amp; services</a></li>
                            <li><a href="{{ route('home') }}#contact" class="transition hover:text-white">Contact</a></li>
                            <li>
                                @auth
                                    <a href="{{ route('account.orders.index') }}" class="transition hover:text-white">Orders</a>
                                @else
                                    <a href="{{ route('login') }}" class="transition hover:text-white">Orders</a>
                                @endauth
                            </li>
                        </ul>
                    </nav>
                </section>

                {{-- Social --}}
                <section id="wishlist" aria-labelledby="footer-heading-social" class="flex flex-col">
                    <h2 id="footer-heading-social" class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#ffd700]">Social</h2>
                    <div class="mt-3 flex gap-4 text-white/75">
                        <a href="#" class="transition hover:text-white" aria-label="Facebook"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="#" class="transition hover:text-white" aria-label="X"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                        <a href="#" class="transition hover:text-white" aria-label="Instagram"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.67-.072-4.949-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                    </div>
                </section>

                {{-- Contact --}}
                <section id="contact" aria-labelledby="footer-heading-contact" class="flex flex-col">
                    <h2 id="footer-heading-contact" class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#ffd700]">Contact</h2>
                    <div class="mt-3 space-y-2 text-sm text-white/80">
                        @if ($telHref !== '')
                            <p>
                                <a href="tel:{{ $telHref }}" class="text-white transition hover:text-[#ffd700]">{{ $footerPhone }}</a>
                            </p>
                        @else
                            <p class="text-white">{{ $footerPhone }}</p>
                        @endif
                        <p>
                            <a href="mailto:{{ $storeEmail }}" class="break-all text-[#ffd700] transition hover:text-white">{{ $storeEmail }}</a>
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <div class="border-t border-white/15 bg-[#00479a] py-3.5 text-[12px] text-white/65">
        <div class="store-box flex flex-col gap-1.5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-x-6">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p class="text-white/55">Genuine devices · Ghana (GHS)</p>
        </div>
    </div>
</footer>
