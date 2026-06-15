@extends('layouts.admin')
@section('title', 'Manajemen Reservasi')
@section('subtitle', 'Kelola reservasi meja pelanggan')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ role_route('admin.reservations.index') }}" class="admin-live-search w-full md:flex-1 flex items-center gap-3 flex-wrap" data-live-search="reservations" data-live-target="#reservations-list">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" placeholder="Cari nama atau nomor..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-medium focus:ring-4 focus:ring-purple-500/10 focus:bg-white transition-all shadow-inner">
                </div>
                <div class="relative w-full sm:w-48">
                    <input type="text" name="date" id="date-picker" value="{{ request('date') }}" placeholder="Pilih Tanggal" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-medium focus:ring-4 focus:ring-purple-500/10 shadow-inner">
                    <svg class="w-5 h-5 absolute right-3 top-3 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Filter
                </button>
                @if(request()->anyFilled(['date', 'status', 'search']))
                    <a href="{{ role_route('admin.reservations.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-200 transition-all">Reset</a>
                @endif
            </form>
        </div>

        <div class="flex items-center gap-2 pt-4 border-t border-slate-100 flex-wrap">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Status:</span>
            <a href="{{ role_route('admin.reservations.index', ['search' => request('search'), 'date' => request('date')]) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ !request('status') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Semua
            </a>
            <a href="{{ role_route('admin.reservations.index', ['status' => 'pending', 'search' => request('search'), 'date' => request('date')]) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'pending' ? 'bg-yellow-500 text-white shadow-lg shadow-yellow-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pending
            </a>
            <a href="{{ role_route('admin.reservations.index', ['status' => 'confirmed', 'search' => request('search'), 'date' => request('date')]) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'confirmed' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Dikonfirmasi
            </a>
            <a href="{{ role_route('admin.reservations.index', ['status' => 'rejected', 'search' => request('search'), 'date' => request('date')]) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'rejected' ? 'bg-red-500 text-white shadow-lg shadow-red-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Ditolak
            </a>
        </div>
    </div>

    <div id="reservations-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($reservations as $res)
        @php
            $statusConfig = match($res->status) { 
                'pending' => ['border' => 'border-yellow-200', 'bg' => 'bg-yellow-50/50', 'badge' => 'bg-yellow-100 text-yellow-700', 'icon' => 'bg-yellow-500', 'dot' => 'bg-yellow-500'],
                'confirmed' => ['border' => 'border-emerald-200', 'bg' => 'bg-emerald-50/50', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'bg-emerald-500', 'dot' => 'bg-emerald-500'],
                'rejected' => ['border' => 'border-red-200', 'bg' => 'bg-red-50/50', 'badge' => 'bg-red-100 text-red-700', 'icon' => 'bg-red-500', 'dot' => 'bg-red-500'],
                default => ['border' => 'border-slate-200', 'bg' => 'bg-slate-50/50', 'badge' => 'bg-slate-100 text-slate-700', 'icon' => 'bg-slate-500', 'dot' => 'bg-slate-500']
            };
            $cfg = $statusConfig;
        @endphp
        <div class="group bg-white rounded-2xl shadow-sm border-2 {{ $cfg['border'] }} {{ $cfg['bg'] }} p-6 hover:shadow-xl transition-all duration-300 overflow-hidden relative">
            <div class="absolute -right-8 -top-8 w-24 h-24 {{ $cfg['icon'] }} rounded-full blur-2xl opacity-10 group-hover:opacity-20 transition-opacity"></div>
            
            <div class="relative">
                <div class="flex items-start justify-between mb-4 pb-4 border-b-2 border-slate-100">
                    <div class="flex-1">
                        <h3 class="font-black text-lg text-slate-900 truncate">{{ $res->name }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Reservasi #{{ $res->id }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} {{ $res->status === 'pending' ? 'animate-pulse' : '' }}"></span>
                        <span class="px-3 py-1 text-xs font-black uppercase tracking-widest rounded-lg {{ $cfg['badge'] }}">{{ $res->status_label }}</span>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.756l2.73 1.365a1 1 0 001.006-.122l4.734-3.762a2 2 0 013.092 2.248l-4.882 7.746a2 2 0 01-1.82.954H5a2 2 0 01-2-2V5z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Kontak</p>
                            <p class="text-sm font-bold text-slate-800">{{ $res->phone }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Tanggal & Waktu</p>
                            <p class="text-sm font-bold text-slate-800">{{ $res->formatted_date }} at {{ $res->formatted_time }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292m15 0A9.002 9.002 0 005.646 9.746m0 0a9 9 0 010 10.508M15 20H9a6 6 0 01-6-6V9a6 6 0 0112 0v5a6 6 0 01-6 6z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Tamu</p>
                            <p class="text-sm font-bold text-slate-800">{{ $res->guests }} orang</p>
                        </div>
                    </div>

                    @if($res->notes)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Catatan</p>
                            <p class="text-xs text-slate-700 line-clamp-2">{{ $res->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                @if($res->status === 'pending')
                <div class="flex gap-2 pt-4 border-t border-slate-200">
                    <form method="POST" action="{{ role_route('admin.reservations.updateStatus', $res) }}" class="flex-1"
                          onsubmit="return confirmReservation(event, 'Konfirmasi reservasi atas nama <b>{{ addslashes($res->name) }}</b>?', 'confirmed', 'question')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Konfirmasi
                        </button>
                    </form>
                    <form method="POST" action="{{ role_route('admin.reservations.updateStatus', $res) }}" class="flex-1"
                          onsubmit="return confirmReservation(event, 'Tolak reservasi atas nama <b>{{ addslashes($res->name) }}</b>?', 'rejected', 'warning')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="w-full py-3 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-black uppercase tracking-widest rounded-xl transition-all active:scale-95">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak
                        </button>
                    </form>
                </div>
                @else
                <div class="pt-4 border-t border-slate-200">
                    <p class="text-xs text-slate-500 text-center italic">Status: {{ $res->status_label }}</p>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-slate-500 font-medium">Tidak ada data reservasi untuk filter yang dipilih</p>
        </div>
        @endforelse
    </div>
    <div>{{ $reservations->links() }}</div>
</div>
@push('scripts')
<script>
    flatpickr("#date-picker", {
        locale: "id",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        allowInput: true
    });

    function confirmReservation(event, message, newStatus, icon) {
        event.preventDefault();
        const form = event.currentTarget;
        const labelMap = { confirmed: 'Konfirmasi', rejected: 'Tolak' };
        const colorMap = { confirmed: '#10b981', rejected: '#ef4444' };

        Swal.fire({
            title: newStatus === 'confirmed' ? 'Konfirmasi Reservasi?' : 'Tolak Reservasi?',
            html: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: colorMap[newStatus] || '#0f172a',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: labelMap[newStatus] || 'Ya',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                const data = await response.json().catch(() => ({ success: true }));

                await Swal.fire({
                    icon: newStatus === 'confirmed' ? 'success' : 'info',
                    title: newStatus === 'confirmed' ? 'Dikonfirmasi!' : 'Ditolak',
                    text: data.message || (newStatus === 'confirmed' ? 'Reservasi berhasil dikonfirmasi!' : 'Reservasi berhasil ditolak.'),
                    timer: 1800,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl' }
                });
                window.location.reload();
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
</script>
@endpush
@endsection
