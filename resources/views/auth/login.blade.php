<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Resto Nusantara Admin</title>
    @vite(['resources/css/app.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="bg-pattern min-h-screen">

    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-amber-400/20 blur-[120px] rounded-full -z-10"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-400/10 blur-[120px] rounded-full -z-10"></div>

    <div class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="max-w-md w-full">

            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center mb-6">
                    <x-resto-logo size="lg" class="drop-shadow-lg" />
                </div>
                <p class="text-slate-500 font-medium italic">Panel Admin</p>
            </div>

            <div class="glass-card rounded-[2.5rem] shadow-2xl shadow-slate-200/50 p-8 md:p-10 border border-white">

                <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="email"
                            class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:bg-white transition-all outline-none text-slate-800 placeholder-slate-400 font-semibold shadow-sm @error('email') border-red-300 bg-red-50 focus:ring-red-500 focus:border-red-500 @enderror">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password"
                            class="block text-xs font-black text-slate-400 uwebppercase tracking-widest mb-2 ml-1">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" 
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:bg-white transition-all outline-none text-slate-800 placeholder-slate-400 font-semibold shadow-sm @error('password') border-red-300 bg-red-50 focus:ring-red-500 focus:border-red-500 @enderror">
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-5 h-5 rounded-xl border-slate-300 bg-white text-amber-500 focus:ring-amber-500 transition-all cursor-pointer">
                        <label for="remember" class="text-sm text-slate-600 font-medium cursor-pointer">Ingat saya</label>
                    </div>

                    <button type="submit"
                        class="group w-full bg-gradient-to-r from-amber-500 to-amber-600 text-white py-4 md:py-5 px-6 rounded-2xl font-black text-base md:text-lg uppercase tracking-widest hover:from-amber-600 hover:to-amber-700 transition-all shadow-xl shadow-amber-600/30 flex items-center justify-center gap-3 active:scale-[0.98] transform hover:scale-[1.02]">
                        <span>Masuk</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <p class="text-center text-slate-500 text-xs mt-6">© {{ date('Y') }} Resto Nusantara. All rights reserved.</p>
        </div>
    </div>

</body>

</html>