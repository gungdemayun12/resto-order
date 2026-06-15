@extends('layouts.admin')
@section('title', 'QR Code Meja ' . $table->number)
@section('subtitle', 'Tampilkan dan unduh QR Code untuk Meja ' . $table->number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <div class="flex flex-col items-center space-y-6">
            <h2 class="text-2xl font-bold text-slate-800">QR Code Meja {{ $table->number }}</h2>
            
            <div class="p-6 bg-white border-2 border-slate-200 rounded-2xl" id="qr-container">
                <div id="qrcode" style="width: 300px; height: 300px; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-amber-200 border-t-amber-600 mx-auto mb-2"></div>
                        <p class="text-sm text-slate-500">Generating QR Code...</p>
                    </div>
                </div>
            </div>

            <p class="text-sm text-slate-500 text-center max-w-md">
                Scan QR Code ini dengan smartphone untuk mengakses menu digital meja {{ $table->number }}
            </p>

            <div class="text-xs text-slate-400 text-center">
                URL: <code class="bg-slate-100 px-2 py-1 rounded">{{ $url }}</code>
            </div>

            
            <div class="flex gap-3 w-full pt-4">
                <button onclick="downloadQR()" class="flex-1 px-4 py-3 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19v-7m0 0V5m0 7H5m7 0h7"/>
                    </svg>
                    Download PNG
                </button>
                <button onclick="printQR()" class="flex-1 px-4 py-3 bg-slate-600 text-white rounded-xl font-semibold hover:bg-slate-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m4 4H9m6 0h.01M9 5h.01M15 5h.01"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Informasi Meja</h3>
        
        <div class="space-y-3">
            <div class="p-4 bg-blue-50 rounded-xl border border-blue-200">
                <p class="text-xs font-medium text-blue-700 uppercase tracking-wide mb-1">Nomor Meja</p>
                <p class="text-3xl font-black text-blue-600">{{ $table->number }}</p>
            </div>

            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                <p class="text-xs font-medium text-emerald-700 uppercase tracking-wide mb-1">Kapasitas</p>
                <p class="text-2xl font-bold text-emerald-600">👥 {{ $table->capacity }} Orang</p>
            </div>

            <div class="p-4 bg-purple-50 rounded-xl border border-purple-200">
                <p class="text-xs font-medium text-purple-700 uppercase tracking-wide mb-1">Lokasi</p>
                <p class="text-lg font-bold text-purple-600">{{ $table->location_label }}</p>
            </div>

            @php
                $statusColor = match($table->status) {
                    'available' => 'bg-emerald-50 border-emerald-200 text-emerald-600',
                    'occupied' => 'bg-red-50 border-red-200 text-red-600',
                    'reserved' => 'bg-amber-50 border-amber-200 text-amber-600',
                };
            @endphp
            <div class="p-4 {{ $statusColor }} rounded-xl border">
                <p class="text-xs font-medium uppercase tracking-wide mb-1">Status Saat Ini</p>
                <p class="text-lg font-bold">{{ $table->status_label }}</p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-200">
            <p class="text-xs text-slate-500 mb-3 font-medium">Instruksi:</p>
            <ol class="text-xs text-slate-600 space-y-2 list-decimal list-inside">
                <li>Cetak atau simpan QR Code</li>
                <li>Tempel di meja restoran</li>
                <li>Pelanggan scan untuk lihat menu digital</li>
                <li>Pelanggan bisa order langsung dari smartphone</li>
            </ol>
        </div>

        <a href="{{ role_route('admin.tables.index') }}" class="block w-full px-4 py-2.5 text-center text-sm font-medium text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
            ← Kembali ke Manajemen Meja
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.5.3/qrcode.min.js"></script>
<script>
    const qrData = '{{ $url }}';
    const qrcodeContainer = document.getElementById('qrcode');
    qrcodeContainer.innerHTML = '';
    
    new QRCode(qrcodeContainer, {
        text: qrData,
        width: 300,
        height: 300,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    function downloadQR() {
        const canvas = document.querySelector('#qrcode canvas');
        if (!canvas) {
            alert('QR Code belum siap. Silakan tunggu sebentar.');
            return;
        }
        
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = `QR-Meja-{{ $table->number }}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printQR() {
        const canvas = document.querySelector('#qrcode canvas');
        if (!canvas) {
            alert('QR Code belum siap. Silakan tunggu sebentar.');
            return;
        }

        const printWindow = window.open('', '', 'height=600,width=800');
        const img = new Image();
        img.src = canvas.toDataURL('image/png');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>QR Code Meja {{ $table->number }}</title>
                    <style>
                        body { margin: 0; padding: 20px; display: flex; flex-direction: column; align-items: center; font-family: Arial, sans-serif; }
                        h1 { margin-bottom: 20px; font-size: 24px; }
                        img { border: 2px solid #000; margin-bottom: 20px; }
                        .info { text-align: center; margin-bottom: 20px; }
                        .info p { margin: 5px 0; }
                        @media print { body { padding: 0; } }
                    </style>
                </head>
                <body>
                    <h1>QR Code Meja {{ $table->number }}</h1>
                    <img src="${img.src}" alt="QR Code" width="300">
                    <div class="info">
                        <p><strong>Meja {{ $table->number }}</strong></p>
                        <p>Kapasitas: {{ $table->capacity }} orang</p>
                        <p>Lokasi: {{ $table->location_label }}</p>
                        <p style="margin-top: 20px; font-size: 12px; color: #666;">Scan untuk akses menu digital</p>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }
</script>
@endsection
