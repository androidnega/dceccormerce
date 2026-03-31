@php
    $cartDrawerUrl = route('cart.drawer');
    $wishlistBase = rtrim(url('/wishlist/toggle'), '/');
    $cartAddBase = rtrim(url('/cart/add'), '/');
@endphp
<script>
(function () {
    var cartAddBase = @json($cartAddBase);
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var token = csrf ? csrf.getAttribute('content') : '';

    function updateCartHeader(data) {
        var countEl = document.querySelector('[data-store-cart-count]');
        var totalEl = document.querySelector('[data-store-cart-total]');
        if (countEl && typeof data.cartCount !== 'undefined') {
            var c = Number(data.cartCount || 0);
            countEl.textContent = c > 99 ? '99+' : String(c);
            countEl.classList.add('flex');
            countEl.classList.remove('hidden');
        }
        if (totalEl && data.cartTotalFormatted) {
            totalEl.textContent = data.cartTotalFormatted;
        }
    }

    function animateToCart(fromEl) {
        var cartLink = document.querySelector('[data-store-cart-link]');
        if (!cartLink || !fromEl) return Promise.resolve();
        var cartRect = cartLink.getBoundingClientRect();
        var srcRect = fromEl.getBoundingClientRect();
        if (!cartRect.width || !srcRect.width) return Promise.resolve();
        var ghost = document.createElement('div');
        ghost.setAttribute('aria-hidden', 'true');
        ghost.style.position = 'fixed';
        ghost.style.left = srcRect.left + 'px';
        ghost.style.top = srcRect.top + 'px';
        ghost.style.width = srcRect.width + 'px';
        ghost.style.height = srcRect.height + 'px';
        ghost.style.borderRadius = '8px';
        ghost.style.pointerEvents = 'none';
        ghost.style.zIndex = '200';
        ghost.style.transition = 'transform 520ms cubic-bezier(0.22, 1, 0.36, 1), opacity 520ms ease';
        ghost.style.opacity = '0.95';
        ghost.style.background = 'linear-gradient(135deg, rgba(0,0,0,0.85), rgba(30,30,30,0.9))';
        if (fromEl.tagName === 'IMG') {
            var img = document.createElement('img');
            img.src = fromEl.currentSrc || fromEl.src;
            img.alt = '';
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'contain';
            img.style.borderRadius = 'inherit';
            ghost.style.background = 'transparent';
            ghost.appendChild(img);
        }
        document.body.appendChild(ghost);
        var targetSize = 18;
        var targetX = cartRect.left + cartRect.width / 2 - targetSize / 2;
        var targetY = cartRect.top + cartRect.height / 2 - targetSize / 2;
        var dx = targetX - srcRect.left;
        var dy = targetY - srcRect.top;
        var sx = targetSize / Math.max(srcRect.width, 1);
        var sy = targetSize / Math.max(srcRect.height, 1);
        return new Promise(function (resolve) {
            requestAnimationFrame(function () {
                ghost.style.transform = 'translate(' + dx + 'px, ' + dy + 'px) scale(' + sx + ', ' + sy + ')';
                ghost.style.opacity = '0.12';
            });
            setTimeout(function () {
                ghost.remove();
                cartLink.classList.add('store-cart-pulse');
                setTimeout(function () { cartLink.classList.remove('store-cart-pulse'); }, 520);
                resolve();
            }, 540);
        });
    }

    var drawerRoot = document.getElementById('store-cart-drawer');
    var drawerBody = document.getElementById('store-cart-drawer-body');
    var drawerBackdrop = drawerRoot ? drawerRoot.querySelector('[data-cart-drawer-backdrop]') : null;
    var drawerPanel = drawerRoot ? drawerRoot.querySelector('[data-cart-drawer-panel]') : null;
    var drawerLoading = drawerRoot ? drawerRoot.querySelector('[data-cart-drawer-loading]') : null;

    function setDrawerOpen(open) {
        if (!drawerRoot) return;
        drawerRoot.classList.toggle('pointer-events-none', !open);
        drawerRoot.classList.toggle('opacity-0', !open);
        drawerRoot.setAttribute('aria-hidden', open ? 'false' : 'true');
        document.body.style.overflow = open ? 'hidden' : '';
        if (drawerPanel) {
            drawerPanel.style.transform = open ? 'translateX(0)' : 'translateX(100%)';
        }
    }

    function renderDrawerHtml(html) {
        if (!drawerBody) return;
        drawerBody.innerHTML = html || '';
        wireDrawerForms();
    }

    function wireDrawerForms() {
        if (!drawerBody) return;
        drawerBody.querySelectorAll('[data-cart-update-form]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!token) return;
                var fd = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    body: fd,
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data || !data.ok) return;
                        updateCartHeader(data);
                        if (data.drawerHtml) renderDrawerHtml(data.drawerHtml);
                    })
                    .catch(function () {});
            });
        });
        drawerBody.querySelectorAll('[data-cart-remove-form]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!token) return;
                var fd = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    body: fd,
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data || !data.ok) return;
                        updateCartHeader(data);
                        if (data.drawerHtml) renderDrawerHtml(data.drawerHtml);
                    })
                    .catch(function () {});
            });
        });
    }

    window.StoreCartDrawer = {
        open: function () {
            if (!drawerRoot || !drawerBody) {
                window.location.href = @json(route('cart.index'));
                return;
            }
            setDrawerOpen(true);
            if (drawerLoading) drawerLoading.style.display = 'flex';
            fetch(@json($cartDrawerUrl), {
                headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) {
                    if (!r.ok) throw new Error('load');
                    return r.text();
                })
                .then(function (html) {
                    if (drawerLoading) drawerLoading.style.display = 'none';
                    renderDrawerHtml(html);
                })
                .catch(function () {
                    if (drawerLoading) drawerLoading.style.display = 'none';
                    drawerBody.innerHTML = '<p class="py-8 text-center text-sm text-red-600">Could not load cart.</p>';
                });
        },
        close: function () {
            setDrawerOpen(false);
        },
        refresh: function () {
            fetch(@json($cartDrawerUrl), { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data && data.drawerHtml) renderDrawerHtml(data.drawerHtml);
                    updateCartHeader(data);
                })
                .catch(function () {});
        },
    };

    document.querySelectorAll('[data-cart-drawer-open]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            window.StoreCartDrawer.open();
        });
    });
    if (drawerBackdrop) drawerBackdrop.addEventListener('click', function () { window.StoreCartDrawer.close(); });
    document.querySelectorAll('[data-cart-drawer-close]').forEach(function (b) {
        b.addEventListener('click', function () { window.StoreCartDrawer.close(); });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && drawerRoot && !drawerRoot.classList.contains('opacity-0')) {
            window.StoreCartDrawer.close();
        }
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM' || !form.classList.contains('store-add-cart-form')) return;
        var action = form.getAttribute('action') || '';
        if (action.indexOf('/cart/add/') === -1) return;
        if (!token) return;
        e.preventDefault();
        if (form.dataset.cartAnimating === '1') return;
        form.dataset.cartAnimating = '1';
        var btn = form.querySelector('.store-add-cart-btn');
        var label = form.querySelector('.store-add-cart-label');
        var added = btn ? btn.getAttribute('data-added-label') || 'Added' : 'Added';
        var orig = btn ? btn.getAttribute('data-add-label') || 'Add to cart' : '';
        var sourceImg = form.closest('[data-product-card]') && form.closest('[data-product-card]').querySelector('img');
        var fd = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
            body: fd,
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || !data.ok) throw new Error('add');
                updateCartHeader(data);
                if (btn) {
                    btn.classList.add('scale-95');
                    setTimeout(function () { btn.classList.remove('scale-95'); }, 160);
                }
                if (label) {
                    label.textContent = added;
                    label.classList.add('animate-pulse');
                    setTimeout(function () {
                        label.textContent = orig;
                        label.classList.remove('animate-pulse');
                    }, 1800);
                }
                if (data.drawerHtml && drawerBody) drawBodyIfDrawerOpen(data);
                return animateToCart(sourceImg || btn);
            })
            .catch(function () {
                window.location.href = form.action;
            })
            .finally(function () {
                form.dataset.cartAnimating = '0';
            });

        function drawBodyIfDrawerOpen(data) {
            if (!drawerRoot || drawerRoot.classList.contains('opacity-0')) return;
            renderDrawerHtml(data.drawerHtml);
        }
    });

    document.querySelectorAll('.store-wishlist-btn, .home-wishlist-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.getAttribute('data-wishlist-product-id');
            if (!id || !token) return;
            fetch(@json($wishlistBase) + '/' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
                body: '{}',
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data || !data.ok) return;
                    var inW = !!data.inWishlist;
                    btn.setAttribute('data-in-wishlist', inW ? '1' : '0');
                    btn.setAttribute('aria-pressed', inW ? 'true' : 'false');
                    var o = btn.querySelector('.store-wishlist-outline, .home-wishlist-outline');
                    var f = btn.querySelector('.store-wishlist-filled, .home-wishlist-filled');
                    if (o) o.classList.toggle('hidden', inW);
                    if (f) f.classList.toggle('hidden', !inW);
                })
                .catch(function () {});
        });
    });

    var modal = document.getElementById('product-quick-view');
    if (modal) {
        var backdrop = modal.querySelector('[data-pqv-backdrop]');
        var closeBtn = modal.querySelector('[data-pqv-close]');
        var panel = modal.querySelector('[data-pqv-panel]');
        var bodyEl = document.getElementById('pqv-body');
        function escapeHtml(s) {
            if (s === null || s === undefined) return '';
            var d = document.createElement('div');
            d.textContent = String(s);
            return d.innerHTML;
        }
        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            modal.classList.add('store-modal-enter');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            modal.classList.remove('store-modal-enter');
            document.body.style.overflow = '';
        }
        if (panel) panel.addEventListener('click', function (e) { e.stopPropagation(); });
        document.querySelectorAll('.store-quick-view-btn, .home-quick-view-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var url = btn.getAttribute('data-quick-view-url');
                if (!url || !bodyEl) return;
                openModal();
                bodyEl.innerHTML =
                    '<div class="flex flex-col items-center gap-4 py-14 text-neutral-500" data-pqv-loading>' +
                    '<svg class="h-10 w-10 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' +
                    '<p class="text-sm font-medium">Loading…</p></div>';
                fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { if (!r.ok) throw new Error('load'); return r.json(); })
                    .then(function (data) {
                        var imgBlock = data.mainImage
                            ? '<div class="aspect-square overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50">' +
                              '<img src="' + escapeHtml(data.mainImage) + '" alt="' + escapeHtml(data.name) + '" class="h-full w-full object-contain" loading="lazy" decoding="async">' +
                              '</div>'
                            : '';
                        var specs = Array.isArray(data.specs) ? data.specs : [];
                        var specsBlock = specs.length
                            ? '<dl class="mt-4 space-y-2 border-t border-neutral-100 pt-4 text-sm">' +
                              specs.map(function (s) {
                                  return '<div class="flex items-center justify-between gap-3"><dt class="text-neutral-500">' +
                                      escapeHtml(s.label || '') +
                                      '</dt><dd class="font-medium text-neutral-800">' +
                                      escapeHtml(s.value || '') +
                                      '</dd></div>';
                              }).join('') +
                              '</dl>'
                            : '';
                        var cartForm =
                            data.inStock && token
                                ? '<form action="' + escapeHtml(cartAddBase + '/' + data.id) + '" method="post" class="store-add-cart-form inline">' +
                                  '<input type="hidden" name="_token" value="' + escapeHtml(token) + '">' +
                                  '<input type="hidden" name="quantity" value="1">' +
                                  '<button type="submit" class="rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-gray-800">Add to cart</button></form>'
                                : '';
                        var priceHtml = data.listPriceFormatted
                            ? '<p class="mt-3 flex flex-wrap items-baseline gap-2"><span class="text-lg font-medium tabular-nums text-neutral-400 line-through">' +
                              escapeHtml(data.listPriceFormatted) +
                              '</span><span class="text-2xl font-bold tabular-nums text-neutral-900">' +
                              escapeHtml(data.priceFormatted) +
                              '</span></p>'
                            : '<p class="mt-3 text-2xl font-bold tabular-nums text-neutral-900">' +
                              escapeHtml(data.priceFormatted) +
                              '</p>';
                        var badgeHtml = data.discountBadge
                            ? '<div class="mb-2"><span class="inline-flex rounded-md bg-rose-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">' +
                              escapeHtml(data.discountBadge) +
                              '</span></div>'
                            : '';
                        bodyEl.innerHTML =
                            '<div class="grid gap-8 sm:grid-cols-2 sm:gap-10">' +
                            '<div class="min-w-0">' + imgBlock + '</div>' +
                            '<div class="min-w-0">' +
                            badgeHtml +
                            '<p class="text-xs font-bold uppercase tracking-wider text-neutral-500">' +
                            escapeHtml(data.category) +
                            '</p>' +
                            '<h3 class="mt-2 text-xl font-bold tracking-tight text-neutral-900 sm:text-2xl">' +
                            escapeHtml(data.name) +
                            '</h3>' +
                            priceHtml +
                            specsBlock +
                            '<div class="mt-6 flex flex-wrap gap-3">' +
                            cartForm +
                            '<a href="' +
                            escapeHtml(data.productUrl) +
                            '" class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-5 py-2.5 text-sm font-semibold text-neutral-800 transition hover:bg-neutral-50">Full details</a>' +
                            '</div></div></div>';
                    })
                    .catch(function () {
                        bodyEl.innerHTML = '<p class="py-10 text-center text-sm text-red-600">Could not load this product.</p>';
                    });
            });
        });
        if (backdrop) backdrop.addEventListener('click', closeModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
        });
    }

    function storePad2(n) {
        return n < 10 ? '0' + n : String(n);
    }
    function storeTickFlashCountdowns() {
        document.querySelectorAll('[data-flash-countdown]').forEach(function (el) {
            var end = el.getAttribute('data-end');
            if (!end) return;
            var t = new Date(end).getTime();
            var now = Date.now();
            if (isNaN(t) || t <= now) {
                el.textContent = 'Ended';
                return;
            }
            var sec = Math.floor((t - now) / 1000);
            var d = Math.floor(sec / 86400);
            sec -= d * 86400;
            var h = Math.floor(sec / 3600);
            sec -= h * 3600;
            var m = Math.floor(sec / 60);
            var s = sec % 60;
            el.textContent = (d > 0 ? d + 'd ' : '') + storePad2(h) + ':' + storePad2(m) + ':' + storePad2(s);
        });
    }
    storeTickFlashCountdowns();
    setInterval(storeTickFlashCountdowns, 1000);
})();
</script>
