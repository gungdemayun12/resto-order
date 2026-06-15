<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - RestoNusa Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
            transition: all 0.25s ease;
            margin: 0 12px;
            border: 1px solid transparent;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link:hover {
            background: rgba(30, 41, 59, 0.8);
            color: #ffffff;
            border-color: #334155;
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff !important;
            border-color: rgba(251, 191, 36, 0.4);
            transform: translateX(3px);
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.45);
            font-weight: 700;
        }

        .sidebar-link.active svg {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
            animation: shimmer 2.5s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 200%;
            }
        }

        .sidebar-link svg {
            width: 24px;
            height: 24px;
            display: block;
            flex-shrink: 0;
            opacity: 0.85;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            stroke: currentColor;
            fill: none;
        }

        .sidebar-link:hover svg {
            opacity: 1;
            transform: scale(1.08);
            color: #fbbf24 !important;
            filter: drop-shadow(0 0 6px rgba(245, 158, 11, 0.45));
        }

        .sidebar-link.active svg {
            opacity: 1;
            transform: scale(1.08);
            color: #ffffff !important;
        }

        .sidebar-link {
            font-size: 15px;
        }

        .sidebar-link svg path,
        .sidebar-link svg rect,
        .sidebar-link svg circle {
            stroke-width: 2.5;
        }

        @keyframes badge-pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.75;
                transform: scale(1.1);
            }
        }

        .badge-pulse {
            animation: badge-pulse 1.5s ease-in-out infinite;
        }

        .topbar-blur {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>

<body class="h-full bg-slate-50" x-data="{ mobileMenuOpen: false }">

    <div id="toast-container" class="fixed top-5 right-5 z-100 flex flex-col gap-3"></div>

    <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity duration-300"
        x-transition:leave="transition-opacity duration-300" class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden"
        @click="mobileMenuOpen = false" style="display: none;"></div>

    <aside
        class="fixed top-0 left-0 h-full w-64 bg-slate-900 z-50 flex flex-col border-r border-slate-800 transition-transform duration-300"
        style="box-shadow: 4px 0 24px rgba(0,0,0,0.3);" :class="{
               '-translate-x-full': !mobileMenuOpen,
               'lg:translate-x-0': true
           }" x-cloak>

        <div
            style="position:absolute;top:0;left:0;right:0;height:200px;background:radial-gradient(ellipse at 50% 0%, rgba(245,158,11,0.18) 0%, transparent 70%);pointer-events:none;">
        </div>

        <div style="padding: 20px 16px 16px; position: relative; z-index: 10;">
            <div
                style="display:flex;align-items:center;gap:14px;background:rgba(30,41,59,0.7);padding:12px 14px;border-radius:16px;border:1px solid rgba(51,65,85,0.8);">

                <div style="flex-shrink:0;display:flex;align-items:center;">
                    <x-resto-logo size="sidebar" />
                </div>
                <div>
                    <div
                        style="color:rgba(245,158,11,0.65);font-size:9px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;margin-top:2px;">
                        {{ strtoupper(auth()->user()->role_label) }} Panel
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto no-scrollbar" style="padding: 8px 0 16px; position: relative; z-index: 10;">

            @php
                // Tentukan prefix URL berdasarkan role user yang sedang login
                $urlPrefix = match (auth()->user()->role) {
                    'owner' => 'admin',
                    'cashier' => 'kasir',
                    'kitchen' => 'dapur',
                    default => 'admin',
                };
                $p = fn(string $path) => url($urlPrefix . '/' . $path);
                $isActive = fn(string $path) => request()->is($urlPrefix . '/' . $path) || request()->is($urlPrefix . '/' . $path . '/*');
            @endphp

            <div style="margin-bottom: 20px;">
                <div
                    style="padding: 0 24px 8px; font-size: 10px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #475569;">
                    Utama
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">

                    @if(auth()->user()->hasPermission('view_dashboard'))
                        <a href="{{ $p('dashboard') }}" class="sidebar-link {{ $isActive('dashboard') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            <span
                                style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Dashboard</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('view_orders'))
                        <a href="{{ $p('orders') }}"
                            class="sidebar-link {{ ($isActive('orders') && !request()->is($urlPrefix . '/orders/history') && !request()->is($urlPrefix . '/orders/scan')) ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M17 17h.01" />
                            </svg>
                            <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pesanan
                                Masuk</span>
                            <span id="order-badge"
                                style="display:none;background:#ef4444;color:white;font-size:10px;font-weight:900;padding:2px 7px;border-radius:999px;box-shadow:0 2px 8px rgba(239,68,68,0.5);"
                                class="badge-pulse">0</span>
                        </a>

                        <a href="{{ $p('orders/history') }}"
                            class="sidebar-link {{ request()->is($urlPrefix . '/orders/history') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Riwayat
                                Pesanan</span>
                        </a>

                        @if(auth()->user()->canScanOrders())
                            <a href="{{ $p('orders/scan') }}"
                                class="sidebar-link {{ request()->is($urlPrefix . '/orders/scan') ? 'active' : '' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path
                                        d="M3 7V5a2 2 0 012-2h2M17 3h2a2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2" />
                                    <rect x="7" y="7" width="10" height="10" rx="1" />
                                </svg>
                                <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Scan QR /
                                    Barcode</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <div
                    style="padding: 0 24px 8px; font-size: 10px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #475569;">
                    Manajemen
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">

                    @if(auth()->user()->hasPermission('manage_menu'))
                        <a href="{{ $p('menu') }}" class="sidebar-link {{ $isActive('menu') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 12h16M4 18h7" />
                                <path d="M15 14l3 3 5-5" stroke-width="2.5" />
                            </svg>
                            <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Menu
                                Restoran</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('view_tables'))
                        <a href="{{ $p('tables') }}" class="sidebar-link {{ $isActive('tables') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <path d="M3 14h3v3H3v3h3m4-6h.01M10 17h.01M10 20h.01" />
                            </svg>
                            <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Meja & QR
                                Code</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('view_sessions'))
                        <a href="{{ $p('sessions') }}" class="sidebar-link {{ $isActive('sessions') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Sesi
                                Aktif</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('view_reservations'))
                        <a href="{{ $p('reservations') }}"
                            class="sidebar-link {{ $isActive('reservations') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span
                                style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Reservasi</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('manage_users'))
                        <a href="{{ $p('employees') }}" class="sidebar-link {{ $isActive('employees') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Karyawan</span>
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <div
                    style="padding: 0 24px 8px; font-size: 10px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #475569;">
                    Laporan & Pengaturan
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">

                    @if(auth()->user()->hasPermission('view_reports'))
                        <a href="{{ $p('reports') }}" class="sidebar-link {{ $isActive('reports') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M18 20V10M12 20V4M6 20v-6" />
                            </svg>
                            <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Laporan
                                Penjualan</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('manage_users'))
                        <a href="{{ $p('settings') }}" class="sidebar-link {{ $isActive('settings') && !$isActive('employees') ? 'active' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 4a4 4 0 110 8 4 4 0 010-8zm-7 16a7 7 0 0114 0H5z" />
                            </svg>
                            <span
                                style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pengaturan
                                Akses</span>
                        </a>
                    @endif

                    <a href="{{ $p('profile') }}" class="sidebar-link {{ $isActive('profile') ? 'active' : '' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Profil Saya</span>
                    </a>
                </div>
            </div>
        </nav>

        <div
            style="padding: 14px 16px; border-top: 1px solid #1e293b; background: rgba(15,23,42,0.5); position: relative; z-index: 10;">
            <div style="display: flex; align-items: center; gap: 10px;">

                <div
                    style="width: 38px; height: 38px; background: linear-gradient(135deg, #334155, #1e293b); border-radius: 50%; border: 1.5px solid #334155; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span
                        style="color: white; font-size: 13px; font-weight: 700;">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                </div>

                <div style="flex: 1; min-width: 0;">
                    <div
                        style="color: white; font-size: 13px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </div>
                    <div
                        style="color: #64748b; font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ auth()->user()->email ?? '' }}
                    </div>
                    <div
                        style="color: #94a3b8; font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Role: {{ auth()->user()->role_label }}</div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        style="padding: 7px; color: #64748b; border-radius: 10px; background: transparent; border: none; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;"
                        onmouseover="this.style.color='#f87171';this.style.background='rgba(248,113,113,0.1)'"
                        onmouseout="this.style.color='#64748b';this.style.background='transparent'" title="Logout">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:ml-64 min-h-screen flex flex-col">

        <header class="topbar-blur"
            style="position: sticky; top: 0; z-index: 30; border-bottom: 1px solid rgba(226,232,240,0.7); box-shadow: 0 1px 12px rgba(0,0,0,0.06);">
            <div class="px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-slate-500 hover:text-slate-900 lg:hidden p-2 rounded-lg transition-colors">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        @php
                            $hour = (int) now()->format('H');
                            if ($hour >= 18) $timeGreeting = 'Selamat Malam,';
                            elseif ($hour >= 15) $timeGreeting = 'Selamat Sore,';
                            elseif ($hour >= 12) $timeGreeting = 'Selamat Siang,';
                            else $timeGreeting = 'Selamat Pagi,';
                        @endphp
                        <h2 class="text-2xl font-black text-slate-800 m-0 leading-tight tracking-tight">
                            @yield('greeting', $timeGreeting) <span
                                class="text-black">{{ auth()->user()->name ?? 'Admin' }}</span>
                        </h2>
                        <p class="text-sm text-slate-500 m-0 mt-1 font-medium">
                            @yield('subtitle', 'Ringkasan performa Anda hari ini')
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 md:gap-4 overflow-x-auto pb-2 md:pb-0 no-scrollbar">

                    <div x-data="{ showSearch: false, searchQuery: '', searchResults: [], searching: false }" class="relative shrink-0">
                        <div class="flex items-center h-10">
                            <div class="relative hidden sm:block h-full flex items-center">
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchGlobal()" @focus="showSearch = true" @click.outside="showSearch = false"
                                    class="w-56 h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-sm transition-all placeholder:text-slate-400"
                                    placeholder="Cari pesanan, menu, meja...">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <button type="button" @click="showSearch = !showSearch" class="sm:hidden p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                        <div x-show="showSearch && searchQuery.length >= 2" x-transition.opacity.duration.200ms
                            class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50" style="display: none;" x-cloak>
                            <div class="px-4 py-2 border-b border-slate-50">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hasil Pencarian</h4>
                            </div>
                            <template x-if="searching">
                                <div class="px-4 py-6 text-center">
                                    <div class="w-6 h-6 border-2 border-amber-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                                    <p class="text-xs text-slate-400 mt-2">Mencari...</p>
                                </div>
                            </template>
                            <template x-if="!searching && searchResults.length === 0 && searchQuery.length >= 2">
                                <div class="px-4 py-6 text-center">
                                    <p class="text-sm text-slate-400">Tidak ditemukan</p>
                                </div>
                            </template>
                            <template x-for="result in searchResults" :key="result.title">
                                <a :href="result.url" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                                        :class="result.color === 'amber' ? 'bg-amber-100 text-amber-600' : result.color === 'blue' ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <template x-if="result.type === 'order'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></template>
                                            <template x-if="result.type === 'menu'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></template>
                                            <template x-if="result.type === 'table'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></template>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 truncate" x-text="result.title"></p>
                                        <p class="text-xs text-slate-400 truncate" x-text="result.subtitle"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>

                    <div class="relative flex items-center bg-white border border-slate-200 rounded-xl px-3 h-10 shadow-sm shrink-0 hover:border-amber-500 transition-colors cursor-pointer group/date" onclick="this.querySelector('input').focus()">
                        <svg class="w-4 h-4 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <input type="text" id="global-date-picker"
                            class="global-flatpickr text-sm font-semibold text-slate-700 focus:outline-none w-24 bg-transparent cursor-pointer"
                            placeholder="Pilih Tanggal" value="{{ date('d/m/Y') }}">
                    </div>

                    <div class="flex items-center gap-2 border-l border-slate-200 pl-4 shrink-0">

                        <div x-data="{ open: false }" class="relative ml-2">
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                class="w-9 h-9 rounded-full bg-slate-200 overflow-hidden border-2 border-white shadow-sm flex items-center justify-center shrink-0 cursor-pointer hover:ring-2 hover:ring-amber-500 transition-all">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=f59e0b&color=fff&bold=true&size=36"
                                    alt="Profile" class="w-full h-full object-cover">
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms
                                class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50"
                                style="display: none;" x-cloak>
                                <div class="px-4 py-3 border-b border-slate-50 mb-1">
                                    <p class="text-sm font-bold text-slate-800 truncate">
                                        {{ auth()->user()->name ?? 'Admin' }}
                                    </p>
                                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                                    <span class="inline-block mt-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full {{ auth()->user()->role === 'owner' ? 'bg-amber-100 text-amber-700' : (auth()->user()->role === 'cashier' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">{{ auth()->user()->role_label }}</span>
                                </div>
                                <a href="{{ role_route('admin.profile') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-amber-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    Pengaturan Akun
                                </a>
                                <a href="{{ role_route('admin.notifications.index') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-amber-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                    Notifikasi
                                </a>
                                <div class="border-t border-slate-50 mt-1 pt-1"></div>
                                <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main style="padding: 24px;" id="main-content">

            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: '{{ session("success") }}',
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: '{{ session("error") }}',
                            timer: 4000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    });
                </script>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const colors = {
                success: '#22c55e',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            const icons = {
                success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            };
            const toast = document.createElement('div');
            toast.style.cssText = `
                background: ${colors[type] || colors.success};
                color: white;
                padding: 12px 18px;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.2);
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 14px;
                font-weight: 500;
                transform: translateX(120%);
                transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                min-width: 240px;
                max-width: 360px;
            `;
            toast.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0;">
                    <path d="${icons[type] || icons.success}"/>
                </svg>
                <span>${message}</span>
            `;
            container.appendChild(toast);
            requestAnimationFrame(() => {
                requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; });
            });
            setTimeout(() => {
                toast.style.transform = 'translateX(120%)';
                setTimeout(() => toast.remove(), 400);
            }, 4500);
        }

        /**
         * Submit form status pesanan via POST (fetch), hindari GET akibat redirect/form.submit().
         */
        function confirmStatusUpdate(event, title, icon = 'question') {
            event.preventDefault();
            const form = event.currentTarget;

            Swal.fire({
                title: title,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            }).then(async (result) => {
                if (!result.isConfirmed) {
                    return;
                }

                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    const data = await response.json().catch(() => ({}));

                    if (response.ok) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Status berhasil diperbarui!',
                            timer: 1800,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-2xl' }
                        });

                        if (typeof window.refreshOrdersList === 'function') {
                            window.refreshOrdersList(false);
                        } else {
                            window.location.reload();
                        }
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Tidak dapat memperbarui status. Silakan coba lagi.',
                        customClass: { popup: 'rounded-2xl' }
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Bermasalah',
                        text: 'Periksa jaringan lalu coba lagi.',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            });

            return false;
        }

        function submitOrderStatus(status) {
            const form = document.getElementById('statusForm');
            if (!form) {
                return;
            }
            const titles = {
                processing: 'Terima pesanan ini dan mulai masak?',
                ready: 'Pesanan sudah siap saji?',
                completed: 'Selesaikan pesanan ini?',
                cancelled: 'Tolak pesanan ini?',
            };
            document.getElementById('statusInput').value = status;
            confirmStatusUpdate(
                { preventDefault: () => { }, currentTarget: form },
                titles[status] || 'Update status pesanan?',
                status === 'cancelled' ? 'warning' : 'question'
            );
        }
    </script>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.Echo) return;

                window.Echo.channel('admin-orders')
                    .listen('.OrderUpdated', function (e) {
                        window.dispatchEvent(new CustomEvent('order-updated', { detail: e }));

                        const badge = document.getElementById('order-badge');
                        if (badge && e.pending_count > 0) {
                            badge.textContent = e.pending_count > 99 ? '99+' : e.pending_count;
                            badge.style.display = 'inline';
                        } else if (badge) {
                            badge.style.display = 'none';
                        }
                    });
            });
        </script>
    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const debounce = (fn, delay = 250) => {
                let timer;
                return function (...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(this, args), delay);
                };
            };

            const parseHtml = function (htmlString) {
                return new DOMParser().parseFromString(htmlString, 'text/html');
            };

            document.querySelectorAll('form.admin-live-search').forEach(function (form) {
                const searchInput = form.querySelector('input[name="search"]');
                if (!searchInput) {
                    return;
                }

                const liveSearchType = form.dataset.liveSearch;
                const feedUrl = form.dataset.feedUrl;
                const liveTargetSelector = form.dataset.liveTarget || '#orders-list';
                const liveTarget = liveTargetSelector ? document.querySelector(liveTargetSelector) : null;

                const updateUrl = function (url) {
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, '', url);
                    }
                };

                const fetchLiveHtml = function (url, target) {
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                        credentials: 'same-origin',
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(function (html) {
                            const doc = parseHtml(html);
                            const newContent = doc.querySelector(liveTargetSelector);
                            if (newContent && target) {
                                target.innerHTML = newContent.innerHTML;
                            }
                        })
                        .catch(function (error) {
                            console.error('Live search failed:', error);
                        });
                };

                const submitSearch = debounce(function () {
                    const query = new URLSearchParams(new FormData(form)).toString();
                    const actionUrl = form.getAttribute('action') || window.location.pathname;
                    const url = (feedUrl || actionUrl) + (query ? '?' + query : '');

                    if (liveSearchType === 'orders' && feedUrl && liveTarget) {
                        fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                        })
                            .then(function (response) {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(function (data) {
                                if (data.html && liveTarget) {
                                    liveTarget.innerHTML = data.html;
                                }
                                if (data.total !== undefined) {
                                    const totalEl = document.getElementById('orders-total-count');
                                    if (totalEl) {
                                        totalEl.textContent = data.total;
                                    }
                                }
                                if (window.updateStatusTabCounts) {
                                    window.updateStatusTabCounts(data.status_counts || {});
                                }
                                const badge = document.getElementById('order-badge');
                                if (badge) {
                                    if (data.pending_count > 0) {
                                        badge.textContent = data.pending_count > 99 ? '99+' : data.pending_count;
                                        badge.style.display = 'inline';
                                    } else {
                                        badge.style.display = 'none';
                                    }
                                }
                                updateUrl(url);
                            })
                            .catch(function (error) {
                                console.error('Order live search failed:', error);
                            });
                    } else if (liveTarget) {
                        fetchLiveHtml(url, liveTarget);
                        updateUrl(url);
                    } else {
                        form.submit();
                    }
                });

                const attachLiveEvents = function () {
                    const searchInput = form.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.addEventListener('input', submitSearch);
                    }
                    form.querySelectorAll('select[name], input[type="checkbox"], input[type="radio"]').forEach(function (field) {
                        field.addEventListener('change', submitSearch);
                    });
                };

                attachLiveEvents();
            });
        });
    </script>

    <script>
        window.openOrderDetail = function(orderId, url) {
            if (!Swal) return alert('SweetAlert tidak tersedia');
            Swal.fire({
                title: 'Memuat...',
                didOpen: () => Swal.showLoading()
            });
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                const statusColors = {
                    pending: 'background:#fef9c3;color:#a16207;border:1px solid #fde68a',
                    payment_success: 'background:#d1fae5;color:#047857;border:1px solid #a7f3d0',
                    processing: 'background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe',
                    ready: 'background:#d1fae5;color:#047857;border:1px solid #a7f3d0',
                    completed: 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0',
                    cancelled: 'background:#fee2e2;color:#b91c1c;border:1px solid #fecaca',
                    pending_confirmation: 'background:#ffedd5;color:#c2410c;border:1px solid #fed7aa',
                    pending_payment: 'background:#fef9c3;color:#a16207;border:1px solid #fde68a',
                };
                const statusStyle = statusColors[data.status] || statusColors.pending;
                const statusIcons = {
                    pending: '⏳', payment_success: '✅', processing: '🍳', ready: '🔔',
                    completed: '✔️', cancelled: '❌', pending_confirmation: '📱', pending_payment: '💳'
                };
                const statusIcon = statusIcons[data.status] || '📋';

                const payStyle = data.payment_method === 'online'
                    ? 'background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe'
                    : 'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0';
                const payIcon = data.payment_method === 'online' ? '💳' : '💵';
                const payLabel = data.payment_method === 'online' ? 'Online' : 'Kasir';

                let itemsHtml = data.items.map((item, idx) => `
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 0;${idx < data.items.length - 1 ? 'border-bottom:1px dashed #e2e8f0;' : ''}">
                        <div style="width:48px;height:48px;background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                            ${item.image_url ? `<img src="${item.image_url}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">` : `<span style="font-size:18px;">🍽️</span>`}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:800;color:#1e293b;margin:0 0 3px;">${item.name}</p>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span style="display:inline-flex;align-items:center;gap:4px;background:#f59e0b;color:#fff;font-size:10px;font-weight:800;padding:2px 8px;border-radius:6px;">${item.quantity}x</span>
                                <span style="font-size:11px;color:#94a3b8;font-weight:500;">@ Rp ${Number(item.price).toLocaleString('id-ID')}</span>
                            </div>
                            ${item.notes ? `<p style="font-size:10px;color:#b45309;background:#fffbeb;border:1px solid #fef3c7;border-radius:6px;padding:3px 8px;margin:5px 0 0;display:inline-block;">📝 ${item.notes}</p>` : ''}
                        </div>
                        <p style="font-size:13px;font-weight:800;color:#0f172a;white-space:nowrap;">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</p>
                    </div>
                `).join('');

                const html = `
                    <div style="font-family:'Inter',sans-serif;text-align:left;">

                        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                            <div>
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                                    <h3 style="font-size:22px;font-weight:900;color:#0f172a;margin:0;letter-spacing:-0.5px;">#${data.order_number}</h3>
                                    <span style="${statusStyle};font-size:10px;font-weight:800;padding:5px 12px;border-radius:10px;text-transform:uppercase;letter-spacing:0.08em;">${statusIcon} ${data.status_label}</span>
                                </div>
                                <p style="font-size:12px;color:#94a3b8;font-weight:500;margin:0;">${data.created_at}</p>
                            </div>
                            <span style="${payStyle};font-size:10px;font-weight:800;padding:5px 12px;border-radius:10px;text-transform:uppercase;letter-spacing:0.08em;">${payIcon} ${payLabel}</span>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;">
                            <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:14px;padding:14px 16px;">
                                <p style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:0.15em;margin:0 0 6px;">Meja</p>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:32px;height:32px;background:#0f172a;color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;">${data.table_number}</div>
                                    <span style="font-size:14px;font-weight:700;color:#1e293b;">Meja ${data.table_number}</span>
                                </div>
                            </div>
                            <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:14px;padding:14px 16px;">
                                <p style="font-size:9px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:0.15em;margin:0 0 6px;">Pelanggan</p>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:32px;height:32px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;">${data.customer_name ? data.customer_name.charAt(0).toUpperCase() : '?'}</div>
                                    <span style="font-size:14px;font-weight:700;color:#1e293b;">${data.customer_name || 'Tamu'}</span>
                                </div>
                            </div>
                        </div>

                        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;margin-bottom:16px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                                <h4 style="font-size:10px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:0.15em;margin:0;">Detail Pesanan</h4>
                                <span style="font-size:10px;font-weight:700;color:#94a3b8;background:#f1f5f9;padding:3px 10px;border-radius:8px;">${data.items.length} item</span>
                            </div>
                            ${itemsHtml || '<p style="font-size:13px;color:#94a3b8;text-align:center;padding:20px 0;">Tidak ada item</p>'}
                        </div>

                        <div style="background:#fff;border:2px solid #e2e8f0;border-radius:16px;padding:20px;margin-bottom:16px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;">
                                <span style="font-size:13px;color:#64748b;font-weight:500;">Subtotal</span>
                                <span style="font-size:13px;font-weight:700;color:#334155;">Rp ${Number(data.subtotal).toLocaleString('id-ID')}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;">
                                <span style="font-size:13px;color:#64748b;font-weight:500;">PPN (11%)</span>
                                <span style="font-size:13px;font-weight:700;color:#334155;">Rp ${Number(data.tax).toLocaleString('id-ID')}</span>
                            </div>
                            <div style="border-top:2px dashed #e2e8f0;margin-top:10px;padding-top:14px;display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.15em;">Total Pembayaran</span>
                                <span style="font-size:24px;font-weight:900;color:#f59e0b;letter-spacing:-0.5px;">${data.formatted_total}</span>
                            </div>
                        </div>

                        ${data.notes ? `<div style="background:#fffbeb;border:1px solid #fef3c7;border-radius:14px;padding:14px 16px;display:flex;align-items:flex-start;gap:10px;margin-bottom:16px;">
                            <span style="font-size:16px;flex-shrink:0;">📝</span>
                            <div><p style="font-size:9px;font-weight:800;color:#92400e;text-transform:uppercase;letter-spacing:0.15em;margin:0 0 4px;">Catatan</p><p style="font-size:12px;color:#b45309;font-weight:500;margin:0;line-height:1.5;">${data.notes}</p></div>
                        </div>` : ''}

                        <div id="admin-receipt-area" style="background:#f1f5f9;border-radius:16px;padding:20px;margin-top:4px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                                <h4 style="font-size:10px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:0.15em;margin:0;">🧾 Preview Struk</h4>
                            </div>
                            <div id="receipt-preview" style="background:#fff;border:1px solid #e2e8f0;border-radius:4px;padding:20px 16px;font-family:'Courier New',Courier,monospace;font-size:12px;color:#000;line-height:1.6;max-width:300px;margin:0 auto;">
                                <div style="text-align:center;padding-bottom:10px;border-bottom:1px dashed #000;margin-bottom:10px;">
                                    <div style="font-size:16px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">Resto Nusantara</div>
                                    <div style="font-size:9px;letter-spacing:1.5px;text-transform:uppercase;opacity:0.5;margin-top:2px;">Sistem Pemesanan Digital</div>
                                </div>
                                <div style="text-align:center;padding:6px 0;border-bottom:1px dashed #000;margin-bottom:10px;">
                                    <span style="display:inline-block;border:2px solid #000;padding:2px 10px;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;transform:rotate(-3deg);">${data.payment_status === 'paid' ? '★ LUNAS ★' : 'BELUM LUNAS'}</span>
                                </div>
                                <div style="padding-bottom:8px;border-bottom:1px dashed #000;margin-bottom:8px;">
                                    <div style="display:flex;justify-content:space-between;margin-bottom:3px;"><span>No. Order</span><span style="font-weight:700;">#${data.order_number}</span></div>
                                    <div style="display:flex;justify-content:space-between;margin-bottom:3px;"><span>Tanggal</span><span>${data.created_at}</span></div>
                                    <div style="display:flex;justify-content:space-between;margin-bottom:3px;"><span>Meja</span><span style="font-weight:700;">${data.table_number}</span></div>
                                    <div style="display:flex;justify-content:space-between;"><span>Bayar</span><span>${data.payment_method === 'online' ? 'Online' : 'Cash'}</span></div>
                                </div>
                                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Detail Pesanan</div>
                                ${data.items.map(item => '<div style="margin-bottom:8px;"><div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;"><div style="flex:1;min-width:0;"><div style="font-weight:700;word-break:break-word;">' + item.name + '</div><div style="font-size:11px;opacity:0.7;">' + item.quantity + ' x Rp ' + Number(item.price).toLocaleString('id-ID') + '</div>' + (item.notes ? '<div style="font-size:10px;font-style:italic;opacity:0.6;padding-left:8px;">↳ ' + item.notes + '</div>' : '') + '</div><span style="font-weight:700;white-space:nowrap;">Rp ' + Number(item.subtotal).toLocaleString('id-ID') + '</span></div></div>').join('')}
                                <div style="border-top:2px dashed #000;margin-top:10px;padding-top:10px;">
                                    <div style="display:flex;justify-content:space-between;margin-bottom:3px;"><span>Subtotal</span><span>Rp ${Number(data.subtotal).toLocaleString('id-ID')}</span></div>
                                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;"><span>PPN (11%)</span><span>Rp ${Number(data.tax).toLocaleString('id-ID')}</span></div>
                                    <div style="border-top:1px dotted #000;padding-top:6px;display:flex;justify-content:space-between;"><span style="font-size:14px;font-weight:700;letter-spacing:1px;">TOTAL</span><span style="font-size:18px;font-weight:700;">${data.formatted_total}</span></div>
                                </div>
                                <div style="border-top:1px dashed #000;margin-top:12px;padding-top:10px;text-align:center;">
                                    <div style="font-size:10px;opacity:0.5;">Terima kasih atas kunjungan Anda!</div>
                                    <div style="font-size:10px;opacity:0.5;margin-top:2px;">Selamat menikmati hidangan Anda</div>
                                </div>
                            </div>
                            <div style="text-align:center;margin-top:14px;">
                                <button onclick="printAdminReceipt()" style="background:#0f172a;color:#fff;border:none;padding:12px 28px;border-radius:12px;font-size:12px;font-weight:800;cursor:pointer;font-family:'Inter',sans-serif;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;" onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#0f172a'">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                                    Cetak Struk
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: 'Detail Pesanan',
                    html: html,
                    width: '600px',
                    showCloseButton: true,
                    showConfirmButton: false,
                    background: '#fff',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl',
                        title: 'text-xl font-black'
                    },
                    didOpen: () => {
                        const el = Swal.getPopup();
                        if (el) el.style.background = '#fff';
                    }
                });
            })
            .catch(err => {
                console.error('Gagal memuat detail:', err);
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memuat detail pesanan' });
            });
        };

        window.printAdminReceipt = function() {
            var el = document.getElementById('receipt-preview');
            if (!el) return alert('Struk tidak ditemukan');
            var w = window.open('', '_blank', 'width=400,height=700');
            w.document.write('<!DOCTYPE html><html><head><title>Cetak Struk</title><style>');
            w.document.write('@page { size: 80mm auto; margin: 0; }');
            w.document.write('*,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }');
            w.document.write('body { font-family: "Courier New", Courier, monospace; font-size: 12px; color: #000; padding: 4mm 3mm 6mm; -webkit-print-color-adjust: exact; print-color-adjust: exact; }');
            w.document.write('</style></head><body>');
            w.document.write(el.innerHTML);
            w.document.write('</body></html>');
            w.document.close();
            setTimeout(function() { w.print(); }, 400);
        };
    </script>

    @auth
        <script>
            // Global search function for Alpine
            function searchGlobal() {
                const input = document.querySelector('[x-model="searchQuery"]');
                if (!input) return;
                const query = input.value;
                if (query.length < 2) return;

                const alpineRoot = input.closest('[x-data]');
                if (!alpineRoot) return;

                // Try Alpine v3 data access
                const alpineData = alpineRoot.__x?.$data || (window.Alpine && Alpine.$data(alpineRoot));
                if (!alpineData) return;

                alpineData.searching = true;

                fetch('{{ role_route("admin.search") }}?q=' + encodeURIComponent(query), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(data => {
                    alpineData.searchResults = data.results || [];
                    alpineData.searching = false;
                })
                .catch(() => {
                    alpineData.searching = false;
                });
            }

            function markAllRead() {
                fetch('{{ role_route("admin.notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(() => {
                    window.location.reload();
                });
            }
        </script>
    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global date picker
            const globalDatePicker = document.getElementById('global-date-picker');
            if (globalDatePicker && typeof flatpickr !== 'undefined') {
                flatpickr(globalDatePicker, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    clickOpens: true,
                    allowInput: false,
                    locale: typeof flatpickr.l10ns?.id !== 'undefined' ? 'id' : 'default',
                    onChange: function(selectedDates, dateStr) {
                        const dateParts = dateStr.split('/');
                        if (dateParts.length === 3) {
                            const isoDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
                            const url = '{{ role_route("admin.dashboard") }}?date_range=today&date=' + isoDate;
                            window.location.href = url;
                        }
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>