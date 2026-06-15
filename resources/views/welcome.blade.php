<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Resto Nusantara - Masakan Autentik Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        amber: { 500: '#f59e0b', 600: '#d97706' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</head>
<body class="h-full bg-slate-900 overflow-hidden">
    <div class="relative h-full w-full flex flex-col items-center justify-center px-6">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-amber-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>

        <div class="relative z-10 text-center max-w-lg w-full">
            <div class="inline-flex items-center justify-center p-4 md:p-5 bg-white/10 backdrop-blur-xl rounded-2xl border border-white/10 mb-10 transform hover:scale-105 transition-transform duration-500">
                <x-resto-logo size="hero" class="drop-shadow-2xl" />
            </div>
            
            <p class="text-slate-400 text-base md:text-lg mb-12 max-w-sm mx-auto leading-relaxed">
                Menyajikan kelezatan warisan kuliner Indonesia dengan sentuhan modern.
            </p>

            <div class="flex flex-col gap-4 w-full">
                @php
                    $testTable = \App\Models\RestaurantTable::first();
                    $qrUrl = $testTable ? route('qr.scan', ['qrToken' => $testTable->qr_token]) : '#';
                @endphp

                @if($testTable)
                <a href="{{ $qrUrl }}" 
                   class="group relative w-full py-5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-black text-sm uppercase tracking-[0.2em] rounded-2xl shadow-2xl shadow-amber-500/20 hover:shadow-amber-500/40 transition-all active:scale-[0.98] overflow-hidden">
<span class="relative z-10 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            Lanjutkan
                        </span>
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('reservasi.index') }}" 
                       class="py-4 bg-white/5 backdrop-blur-md text-white font-bold text-[11px] uppercase tracking-widest rounded-2xl border border-white/10 hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                        📅 Reservasi
                    </a>
                    <a href="{{ route('login') }}" 
                       class="py-4 bg-white/5 backdrop-blur-md text-white font-bold text-[11px] uppercase tracking-widest rounded-2xl border border-white/10 hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                        🔑 Admin
                    </a>
                </div>
            </div>

            <div class="mt-16 pt-8 border-t border-white/5">
                <p class="text-slate-600 text-[10px] font-black uppercase tracking-[0.3em]">
                    Scan QR Code di meja Anda untuk mulai
                </p>
            </div>
        </div>
    </div>
</body>
</html>
