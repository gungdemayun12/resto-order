<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Resto Nusantara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center justify-center p-6 text-center">
    <div class="max-w-sm w-full space-y-8">
        <div class="inline-flex items-center justify-center mb-4">
            <x-resto-logo size="xl" class="drop-shadow-lg" />
        </div>

        <div class="space-y-3">
            <p class="text-slate-500 font-medium">Nikmati kemudahan memesan makanan langsung dari meja Anda.</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100">
            @php
                $testTable = \App\Models\RestaurantTable::first();
                $qrUrl = $testTable ? route('qr.scan', ['qrToken' => $testTable->qr_token]) : '#';
            @endphp

            @if($testTable)
                <div class="mb-6 p-4 bg-slate-50 rounded-3xl border border-dashed border-slate-200 inline-block">
                    {!! QrCode::size(150)->margin(1)->color(30, 41, 59)->generate($qrUrl) !!}
                    <p class="mt-3 text-[10px] font-bold text-slate-400 uppercase">Test QR: Meja #{{ $testTable->number }}</p>
                </div>
            @else
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
            @endif

            <h2 class="text-xl font-bold text-slate-800 mb-2">Silakan Scan QR Code</h2>
            <p class="text-sm text-slate-400 leading-relaxed">Scan QR Code di atas atau yang ada di meja Anda untuk mulai memesan.</p>
        </div>

        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Ketat • Aman • Modern</p>
    </div>
</body>
</html>