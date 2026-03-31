{{--
  Profile menu (top-right pattern). $context: dashboard | rider | store
  Optional: $store_header_dark — light control on blue storefront header (store only).
  Optional: $store_header_icon_rows — icon + two-line label (Log In / Account style) for storefront header.
--}}
@auth
    @php
        $user = auth()->user();
        $name = trim((string) $user->name);
        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
        if ($name === '') {
            $initials = '?';
        } elseif (count($parts) >= 2) {
            $initials = mb_strtoupper(mb_substr($parts[0], 0, 1).mb_substr($parts[count($parts) - 1], 0, 1));
        } else {
            $initials = mb_strtoupper(mb_substr($name, 0, min(2, mb_strlen($name))));
        }
        $context = $context ?? 'store';
        $role = $user->role;
        $storeHeaderDark = ($context === 'store') && ($store_header_dark ?? false);
        $storeHeaderIconRows = ($context === 'store') && ($store_header_icon_rows ?? false);
        $hideProfileEmail = in_array($context ?? '', ['dashboard', 'rider'], true);
    @endphp
    <details class="group relative z-[70]">
        <summary
            class="flex cursor-pointer list-none items-center transition select-none [&::-webkit-details-marker]:hidden
                @if ($storeHeaderIconRows)
                    gap-3 border-0 bg-transparent py-1 pl-0 pr-1 text-white shadow-none transition-colors hover:text-[#ffd700]
                @else
                    gap-2 rounded-xl border py-2 pl-2 pr-3
                    @if ($context === 'dashboard')
                        border-slate-600/70 bg-slate-700/50 text-white shadow-none hover:bg-slate-700/80
                    @elseif ($storeHeaderDark)
                        border-white/25 bg-white/10 text-white shadow-none hover:bg-white/15
                    @else
                        bg-white shadow-sm hover:bg-slate-50
                        @if ($context === 'rider')
                            border-emerald-200 hover:border-emerald-300
                        @else
                            border-slate-200 hover:border-indigo-200
                        @endif
                    @endif
                @endif
            "
            aria-label="Account menu"
        >
            @if ($storeHeaderIconRows)
                <i class="fa-regular fa-user shrink-0 text-[1.5rem] leading-none text-inherit" aria-hidden="true"></i>
                <span class="min-w-0 max-w-[10rem] text-left text-inherit">
                    <span class="block truncate text-sm font-normal leading-snug">{{ $name !== '' ? $name : 'Account' }}</span>
                    <span class="block text-sm font-bold leading-snug">Account</span>
                </span>
                <i class="fa-solid fa-chevron-down ms-0.5 text-[10px] text-inherit opacity-70 transition group-open:rotate-180" aria-hidden="true"></i>
            @else
            <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xs font-bold
                    @if ($context === 'dashboard') bg-[#ff6000] text-white
                    @elseif ($context === 'rider') bg-emerald-600 text-white
                    @elseif ($storeHeaderDark) bg-white text-[#0057b8]
                    @else bg-gradient-to-br from-indigo-600 to-violet-600 text-white
                    @endif
                "
            >{{ $initials }}</span>
            <span class="hidden min-w-0 max-w-[10rem] text-left sm:block">
                <span class="block truncate text-sm font-semibold leading-tight {{ ($context === 'dashboard' || $storeHeaderDark) ? 'text-white' : 'text-slate-900' }}">{{ $user->name }}</span>
                @if (! $hideProfileEmail)
                    <span class="block truncate text-[11px] leading-tight {{ ($context === 'dashboard' || $storeHeaderDark) ? 'text-white/75' : 'text-slate-500' }}">{{ $user->email }}</span>
                @endif
            </span>
            <i class="fa-solid fa-chevron-down ms-1 text-[10px] transition group-open:rotate-180 {{ ($context === 'dashboard' || $storeHeaderDark) ? 'text-white/70' : 'text-slate-400' }}" aria-hidden="true"></i>
            @endif
        </summary>
        <div
            class="absolute right-0 mt-2 w-[min(100vw-2rem,17rem)] overflow-hidden rounded-xl border bg-white py-1 shadow-lg ring-1 ring-black/5
                @if ($context === 'dashboard') border-slate-200
                @elseif ($context === 'rider') border-emerald-200
                @else border-slate-100
                @endif
            "
            role="menu"
        >
            <div class="border-b border-slate-100 px-4 py-3 sm:hidden">
                <p class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                @if (! $hideProfileEmail)
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                @endif
                <p class="mt-1.5 inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-600">
                    @if ($role === 'admin') Admin
                    @elseif ($role === 'manager') Manager
                    @elseif ($role === 'rider') Rider
                    @else Customer
                    @endif
                </p>
            </div>
            <div class="hidden border-b border-slate-100 px-4 py-2 sm:block">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    @if ($role === 'admin') Admin
                    @elseif ($role === 'manager') Manager
                    @elseif ($role === 'rider') Rider
                    @else Signed in
                    @endif
                </p>
            </div>

            @if ($context === 'store')
                @if (in_array($role, ['admin', 'manager'], true))
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50" role="menuitem">
                        <i class="fa-solid fa-grip w-4 text-center text-xs text-slate-400" aria-hidden="true"></i>
                        Dashboard
                    </a>
                @elseif ($role === 'rider')
                    <a href="{{ route('rider.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50" role="menuitem">
                        <i class="fa-solid fa-motorcycle w-4 text-center text-xs text-slate-400" aria-hidden="true"></i>
                        Deliveries
                    </a>
                @else
                    <a href="{{ route('account.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50" role="menuitem">
                        <i class="fa-solid fa-user w-4 text-center text-xs text-slate-400" aria-hidden="true"></i>
                        My account
                    </a>
                    <a href="{{ route('account.orders.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50" role="menuitem">
                        <i class="fa-solid fa-bag-shopping w-4 text-center text-xs text-slate-400" aria-hidden="true"></i>
                        My orders
                    </a>
                @endif
                <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50" role="menuitem">
                    <i class="fa-solid fa-store w-4 text-center text-xs text-slate-400" aria-hidden="true"></i>
                    View store
                </a>
            @else
                <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50" role="menuitem">
                    <i class="fa-solid fa-store w-4 text-center text-xs text-slate-400" aria-hidden="true"></i>
                    View store
                </a>
            @endif

            <form action="{{ route('logout') }}" method="post" role="none">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 border-t border-slate-100 px-4 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-red-50 hover:text-red-700" role="menuitem">
                    <i class="fa-solid fa-right-from-bracket w-4 text-center text-xs" aria-hidden="true"></i>
                    Log out
                </button>
            </form>
        </div>
    </details>
@endauth
