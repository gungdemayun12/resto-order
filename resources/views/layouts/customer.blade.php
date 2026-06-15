<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Menu') - Resto Nusantara</title>
    <meta name="description" content="Pesan makanan dan minuman favorit Anda di Resto Nusantara. Menu digital dengan pemesanan mudah langsung dari meja Anda.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .slide-up-enter { transform: translateY(100%); }
        .slide-up-enter-active { transform: translateY(0); transition: transform 0.3s ease-out; }

        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        .badge-pulse { animation: badgePulse 0.3s ease-out; }

        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-slate-50">

    <div id="toast-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-[200] space-y-2 w-[90vw] max-w-sm"></div>

    @yield('content')

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const icons = {
                success: '✓',
                error: '✕',
                info: 'ℹ',
                cart: '🛒'
            };
            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                cart: 'bg-amber-500'
            };
            toast.className = `${colors[type]} text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 transform -translate-y-4 opacity-0 transition-all duration-300 text-sm font-medium`;
            toast.innerHTML = `<span class="text-lg">${icons[type]}</span><span>${message}</span>`;
            container.appendChild(toast);
            requestAnimationFrame(() => { toast.classList.remove('-translate-y-4', 'opacity-0'); });
            setTimeout(() => {
                toast.classList.add('-translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>
</html>