<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Pembayaran - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Menunggu Konfirmasi</h1>
                <p class="text-gray-600">Silakan tunjukkan barcode ini ke kasir</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Nomor Order</span>
                    <span class="font-mono font-bold text-gray-800">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Meja</span>
                    <span class="font-semibold text-gray-800">{{ $order->table->table_number }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total</span>
                    <span class="font-bold text-lg text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-white border-4 border-gray-200 rounded-xl p-6 mb-6">
                <div class="flex justify-center">
                    {!! QrCode::size(250)->generate($order->order_number) !!}
                </div>
                <p class="text-center text-sm text-gray-500 mt-4">Scan barcode ini di kasir</p>
            </div>

            <div class="text-center">
                <div class="inline-flex items-center space-x-2">
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">Menunggu konfirmasi dari kasir...</p>
            </div>

            <div class="mt-6 border-t pt-4">
                <h3 class="font-semibold text-gray-800 mb-3">Detail Pesanan</h3>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                        <span class="text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        // Poll untuk cek status konfirmasi
        let pollInterval = setInterval(function() {
            fetch('{{ route("customer.order.check-confirmation", $order->order_number) }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Confirmation status:', data);
                    if (data.confirmed) {
                        clearInterval(pollInterval);
                        // Redirect ke halaman sukses
                        window.location.href = '{{ route("customer.order.success", $order->order_number) }}';
                    }
                })
                .catch(error => {
                    console.error('Error checking confirmation:', error);
                });
        }, 3000); // Check setiap 3 detik

        // Clear interval saat halaman ditutup
        window.addEventListener('beforeunload', function() {
            clearInterval(pollInterval);
        });
    </script>
</body>
</html>