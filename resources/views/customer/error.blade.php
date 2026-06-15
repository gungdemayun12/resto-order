<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }} - Restoran</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">

                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ $title ?? 'Terjadi Kesalahan' }}
                </h1>

                <p class="text-gray-600 mb-6">
                    {{ $message ?? 'Maaf, terjadi kesalahan. Silakan coba lagi.' }}
                </p>

                <div class="space-y-3">
                    <a href="{{ route('home') }}" 
                       class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition">
                        Kembali ke Beranda
                    </a>

                    <button onclick="window.history.back()" 
                            class="block w-full bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-300 transition">
                        Kembali
                    </button>
                </div>
            </div>

            <p class="text-center text-sm text-gray-500 mt-4">
                Butuh bantuan? Hubungi staff kami.
            </p>
        </div>
    </div>
</body>
</html>