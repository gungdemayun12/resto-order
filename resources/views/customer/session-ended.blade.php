<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir - Resto Nusantara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen px-6">
    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-10 text-center relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-400/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-400/10 rounded-full blur-3xl"></div>

        <div class="relative">
            <div class="flex justify-center mb-8">
                <x-resto-logo size="md" />
            </div>

            <h1 class="text-3xl font-black text-slate-800 mb-4 tracking-tight">Sesi Selesai</h1>
            <p class="text-slate-500 mb-8 leading-relaxed">Terima kasih telah berkunjung. Sesi meja Anda telah berakhir. Kami tunggu kunjungan Anda berikutnya!</p>

            <div class="space-y-4">
                <a href="{{ route('home') }}" class="block w-full py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 active:scale-95">
                    Kembali ke Beranda
                </a>
                <p class="text-[11px] text-slate-400 font-medium">Jika ini kesalahan, silakan scan QR Code di meja Anda kembali.</p>
            </div>
        </div>
    </div>
</body>
</html>