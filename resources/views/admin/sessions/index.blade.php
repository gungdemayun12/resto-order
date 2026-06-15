@extends('layouts.admin')
@section('title', 'Manajemen Sesi Meja')
@section('subtitle', 'Pantau dan kelola sesi aktif pelanggan di setiap meja')

@section('content')
<div class="space-y-6" x-data="sessionManager()">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-lg hover:shadow-slate-100/50 transition-all group">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $activeSessions->count() }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sesi Aktif</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-lg hover:shadow-slate-100/50 transition-all group">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-800 group-hover:text-amber-600 transition-colors">{{ $expiredSessions->count() }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Expired (Hari Ini)</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <button @click="cleanupSessions()" class="w-full h-full flex items-center justify-center gap-2 bg-slate-900 text-white font-bold rounded-xl hover:bg-amber-500 transition-all px-6 py-3 active:scale-95 shadow-lg shadow-slate-900/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Bersihkan Sesi Expired
            </button>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Sesi Meja Aktif</h3>
                    <p class="text-[10px] text-slate-400 font-medium">{{ $activeSessions->count() }} meja sedang terisi</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="pl-6 pr-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Meja</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Customer</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Mulai</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Berakhir</th>
                        <th class="pl-4 pr-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($activeSessions as $session)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="pl-6 pr-4 py-4">
                            <div class="w-10 h-10 bg-slate-900 text-white font-black rounded-xl flex items-center justify-center shadow-sm">
                                {{ $session->table->number }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm">
                                    {{ substr($session->orders->first()->customer_name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $session->orders->first()->customer_name ?? 'Guest' }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium tracking-tight truncate max-w-[120px]">{{ $session->session_token }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-xs font-bold text-slate-700">
                            {{ $session->started_at->format('H:i') }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-bold text-slate-700">{{ $session->expires_at->format('H:i') }}</span>
                                <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    @php
                                        $total = $session->started_at->diffInMinutes($session->expires_at);
                                        $passed = $session->started_at->diffInMinutes(now());
                                        $percent = min(100, max(0, ($passed / $total) * 100));
                                    @endphp
                                    <div class="h-full rounded-full transition-all {{ $percent > 80 ? 'bg-red-500' : ($percent > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="pl-4 pr-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ role_route('admin.sessions.show', $session->id) }}" class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="Detail Sesi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button @click="closeSession({{ $session->id }}, '{{ $session->table->number }}')" class="p-2.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Tutup Sesi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <p class="text-slate-500 font-bold">Tidak ada sesi aktif</p>
                                <p class="text-xs text-slate-400 mt-1">Semua meja kosong saat ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sessionManager() {
    return {
        async closeSession(id, tableNumber) {
            const result = await Swal.fire({
                title: 'Konfirmasi',
                text: `Tutup sesi meja #${tableNumber}? Semua pesanan di sesi ini harus sudah selesai.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Tutup Sesi!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;
            
            try {
                const res = await fetch('/{{ role_prefix() }}/sessions/' + id + '/close', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (data.require_confirmation) {
                        const forceResult = await Swal.fire({
                            title: 'Tutup Paksa?',
                            text: data.message + ' Tetap tutup paksa?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#0f172a',
                            cancelButtonColor: '#ef4444',
                            confirmButtonText: 'Ya, Tutup Paksa!',
                            cancelButtonText: 'Batal'
                        });
                        
                        if (forceResult.isConfirmed) {
                            this.closeForce(id);
                        }
                    } else {
                        showToast(data.message, 'error');
                    }
                }
            } catch (e) {
                showToast('Gagal memproses permintaan', 'error');
            }
        },
        
        async closeForce(id) {
            try {
                const res = await fetch('/{{ role_prefix() }}/sessions/' + id + '/close', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ force: true })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Sesi ditutup paksa', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (e) { showToast('Gagal', 'error'); }
        },

        async cleanupSessions() {
            const result = await Swal.fire({
                title: 'Bersihkan Sesi',
                text: 'Bersihkan semua sesi yang sudah expired?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Bersihkan!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            try {
                const res = await fetch('{{ role_route('admin.sessions.expire') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await res.json();
                showToast(data.message, 'info');
                setTimeout(() => window.location.reload(), 1000);
            } catch (e) { showToast('Gagal', 'error'); }
        }
    }
}
</script>
@endpush
@endsection
