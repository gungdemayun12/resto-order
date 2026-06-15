@extends('layouts.admin')
@section('title', 'Panduan Dashboard Management')
@section('subtitle', 'Pelajari semua fitur dan cara menggunakan management dashboard baru')

@section('content')
<div class="space-y-8">
    <div class="bg-linear-to-r from-slate-900 to-slate-800 rounded-2xl p-8 text-white shadow-2xl">
        <div class="flex items-center gap-4 mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h1 class="text-3xl font-black">Dashboard Management - Fitur Baru</h1>
        </div>
        <p class="text-slate-300">Selamat datang! Berikut adalah panduan lengkap menggunakan dashboard management yang telah diperbarui dengan filter dan icon yang lebih baik.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-6 border border-amber-100 shadow-sm hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-linear-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center mb-4 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <h3 class="font-black text-slate-900 mb-2">Menu Management</h3>
            <p class="text-sm text-slate-600 mb-4">Kelola menu restoran dengan filter kategori, status, dan pencarian real-time</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li>✓ Filter berdasarkan kategori</li>
                <li>✓ Filter status (Tersedia/Habis)</li>
                <li>✓ Pencarian cepat</li>
                <li>✓ Bulk actions</li>
            </ul>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-emerald-100 shadow-sm hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-linear-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-black text-slate-900 mb-2">Meja & QR Code</h3>
            <p class="text-sm text-slate-600 mb-4">Kelola tata letak meja dengan QR code digital untuk pelanggan</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li>✓ Filter status meja</li>
                <li>✓ QR code generation</li>
                <li>✓ Statistik penggunaan</li>
                <li>✓ Lokasi tracking</li>
            </ul>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-blue-100 shadow-sm hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-linear-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mb-4 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="font-black text-slate-900 mb-2">Pesanan Masuk</h3>
            <p class="text-sm text-slate-600 mb-4">Proses pesanan pelanggan dengan tracking real-time</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li>✓ Real-time notifications</li>
                <li>✓ Status filtering</li>
                <li>✓ Customer search</li>
                <li>✓ Date range filtering</li>
            </ul>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-linear-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mb-4 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-black text-slate-900 mb-2">Reservasi</h3>
            <p class="text-sm text-slate-600 mb-4">Kelola pemesanan meja dengan status confirmation</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li>✓ Status filtering</li>
                <li>✓ Date picker</li>
                <li>✓ Guest count tracking</li>
                <li>✓ Quick actions</li>
            </ul>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-amber-50 to-amber-100 px-8 py-6 border-b border-amber-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Cara Menggunakan Menu Management</h3>
                </div>
            </div>
            <div class="p-8 space-y-4">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-700 font-black text-sm">1</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Klik "Menu Restoran" di Dashboard</h4>
                        <p class="text-sm text-slate-600 mt-1">Dari card management di dashboard utama, pilih menu management untuk masuk ke halaman pengelolaan menu.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-700 font-black text-sm">2</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Gunakan Filter yang Tersedia</h4>
                        <p class="text-sm text-slate-600 mt-1">
                            • <strong>Search:</strong> Cari menu berdasarkan nama<br>
                            • <strong>Kategori:</strong> Filter berdasarkan kategori menu<br>
                            • <strong>Status:</strong> Tampilkan menu tersedia atau habis
                        </p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-700 font-black text-sm">3</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Kelola Menu Individual</h4>
                        <p class="text-sm text-slate-600 mt-1">Hover di atas card menu untuk melihat tombol edit dan delete. Edit untuk mengubah detail menu, delete untuk menghapus.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-700 font-black text-sm">4</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Toggle Status Ketersediaan</h4>
                        <p class="text-sm text-slate-600 mt-1">Klik tombol status (Tersedia/Habis) di card menu untuk dengan cepat mengubah status ketersediaan menu.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-emerald-50 to-emerald-100 px-8 py-6 border-b border-emerald-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Cara Menggunakan Tables Management</h3>
                </div>
            </div>
            <div class="p-8 space-y-4">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-700 font-black text-sm">1</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Monitor Statistik Meja</h4>
                        <p class="text-sm text-slate-600 mt-1">Di bagian atas halaman, lihat statistik real-time: berapa meja tersedia dan berapa yang sedang terisi.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-700 font-black text-sm">2</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Filter Berdasarkan Status</h4>
                        <p class="text-sm text-slate-600 mt-1">Gunakan tombol filter untuk menampilkan hanya meja yang tersedia, terisi, atau dipesan.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-700 font-black text-sm">3</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Generate QR Code</h4>
                        <p class="text-sm text-slate-600 mt-1">Setiap meja memiliki tombol QR Code unik. Klik untuk menampilkan/print QR code untuk pelanggan.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-700 font-black text-sm">4</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Kelola Sesi Pelanggan</h4>
                        <p class="text-sm text-slate-600 mt-1">Jika ada sesi aktif, tombol "Tutup Sesi" akan muncul. Gunakan untuk mengakhiri sesi meja.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-blue-50 to-blue-100 px-8 py-6 border-b border-blue-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Cara Menggunakan Orders Management</h3>
                </div>
            </div>
            <div class="p-8 space-y-4">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-700 font-black text-sm">1</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Monitor Real-time Indicator</h4>
                        <p class="text-sm text-slate-600 mt-1">Indicator berwarna hijau di atas menunjukkan real-time monitoring aktif. Pesanan akan muncul otomatis tanpa refresh.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-700 font-black text-sm">2</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Gunakan Tab Status</h4>
                        <p class="text-sm text-slate-600 mt-1">Tab status menampilkan jumlah pesanan di setiap tahap: Menunggu, Pembayaran Berhasil, Diproses, Siap, Selesai, Dibatalkan.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-700 font-black text-sm">3</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Cari & Filter Pesanan</h4>
                        <p class="text-sm text-slate-600 mt-1">Cari berdasarkan nomor order atau nama pelanggan. Filter berdasarkan tanggal untuk laporan spesifik.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-700 font-black text-sm">4</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Kelola Status Pesanan</h4>
                        <p class="text-sm text-slate-600 mt-1">Update status pesanan sesuai tahapan proses: dari pending hingga completed. Setiap tahap memiliki action button tersendiri.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="bg-linear-to-r from-purple-50 to-purple-100 px-8 py-6 border-b border-purple-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Cara Menggunakan Reservations Management</h3>
                </div>
            </div>
            <div class="p-8 space-y-4">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-700 font-black text-sm">1</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Filter Reservasi</h4>
                        <p class="text-sm text-slate-600 mt-1">Gunakan filter untuk mencari reservasi berdasarkan nama, tanggal, dan status (Pending/Dikonfirmasi/Ditolak).</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-700 font-black text-sm">2</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Lihat Detail Lengkap</h4>
                        <p class="text-sm text-slate-600 mt-1">Setiap card reservasi menampilkan: nama pelanggan, nomor kontak, tanggal/waktu, jumlah tamu, dan catatan khusus.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-700 font-black text-sm">3</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Action Pending Reservasi</h4>
                        <p class="text-sm text-slate-600 mt-1">Untuk reservasi pending, Anda bisa langsung konfirmasi atau menolak dengan satu klik.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 flex items-start pt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-700 font-black text-sm">4</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900">Track Status</h4>
                        <p class="text-sm text-slate-600 mt-1">Status visual dengan warna dan ikon memudahkan tracking: kuning (pending), hijau (confirmed), merah (rejected).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-linear-to-r from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
        <div class="flex items-center gap-3 mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <h3 class="text-2xl font-black">Tips & Tricks untuk Efisiensi Maksimal</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4">
                <p class="font-bold mb-2">⚡ Gunakan Keyboard Shortcuts</p>
                <p class="text-sm opacity-90">Ctrl+F untuk cepat membuka search, Tab untuk navigate antar tab status</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4">
                <p class="font-bold mb-2">📱 Monitor Real-time</p>
                <p class="text-sm opacity-90">Dashboard akan otomatis update tanpa perlu refresh browser Anda</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4">
                <p class="font-bold mb-2">🎯 Batch Operations</p>
                <p class="text-sm opacity-90">Gunakan filter untuk quickly manage multiple items sekaligus</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4">
                <p class="font-bold mb-2">📊 Export Reports</p>
                <p class="text-sm opacity-90">Data dapat di-export untuk analisis lebih lanjut di spreadsheet</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
        <h3 class="text-2xl font-black text-slate-900 mb-6">✨ Highlight Fitur-Fitur Baru</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex gap-4">
                <div class="text-3xl">🎨</div>
                <div>
                    <h4 class="font-bold text-slate-900">Desain Modern dengan Tailwind</h4>
                    <p class="text-sm text-slate-600 mt-1">Semua komponen menggunakan Tailwind CSS untuk design yang konsisten dan responsif di semua device.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="text-3xl">📊</div>
                <div>
                    <h4 class="font-bold text-slate-900">Real-time Dashboard</h4>
                    <p class="text-sm text-slate-600 mt-1">Statistik dan data terupdate otomatis menggunakan Laravel Echo untuk notifikasi real-time.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="text-3xl">🔍</div>
                <div>
                    <h4 class="font-bold text-slate-900">Advanced Filtering</h4>
                    <p class="text-sm text-slate-600 mt-1">Filter multi-level untuk setiap management section membuat pencarian data menjadi sangat mudah.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="text-3xl">⚡</div>
                <div>
                    <h4 class="font-bold text-slate-900">Performance Optimized</h4>
                    <p class="text-sm text-slate-600 mt-1">Database queries dioptimasi dengan proper indexing dan lazy loading untuk performa terbaik.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="text-3xl">📱</div>
                <div>
                    <h4 class="font-bold text-slate-900">Mobile Responsive</h4>
                    <p class="text-sm text-slate-600 mt-1">Semua halaman management fully responsive dan berfungsi sempurna di tablet dan smartphone.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="text-3xl">🔐</div>
                <div>
                    <h4 class="font-bold text-slate-900">Secure & Validated</h4>
                    <p class="text-sm text-slate-600 mt-1">Semua input divalidasi dan diproteksi dengan CSRF tokens dan authorization checks.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
