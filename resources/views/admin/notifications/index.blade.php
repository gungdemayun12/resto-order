@extends('layouts.admin')
@section('title', 'Notifikasi')
@section('subtitle', 'Semua notifikasi dan pesan terbaru Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Notifikasi Anda</h3>
                    <p class="text-xs text-slate-400">{{ $unreadCount }} belum dibaca</p>
                </div>
            </div>
            @if($unreadCount > 0)
                <button onclick="markAllRead(this)" class="px-4 py-2 bg-amber-50 text-amber-600 text-xs font-bold rounded-xl hover:bg-amber-100 transition-all border border-amber-100">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="divide-y divide-slate-50">
            @forelse($notifications as $notif)
                @php
                    $iconBg = match($notif->color) { 'emerald' => 'bg-emerald-100', 'blue' => 'bg-blue-100', 'red' => 'bg-red-100', 'slate' => 'bg-slate-100', default => 'bg-amber-100' };
                    $emoji = match($notif->icon) { 'check' => '✅', 'x' => '❌', 'cart' => '🛒', 'cooking' => '🍳', 'calendar' => '📅', 'bell' => '🔔', default => '🔔' };
                @endphp
                <div class="flex items-start gap-4 px-6 py-5 hover:bg-slate-50/80 transition-colors group {{ $notif->is_read ? '' : 'bg-amber-50/30' }}">
                    <div class="w-10 h-10 {{ $iconBg }} rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="text-lg">{{ $emoji }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-800">{{ $notif->title }}</h4>
                            @if(!$notif->is_read)
                                <span class="w-2 h-2 bg-amber-500 rounded-full shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $notif->body }}</p>
                        <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if(!$notif->is_read)
                            <button onclick="markAsRead({{ $notif->id }}, this)" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Tandai dibaca">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                        <button onclick="deleteNotification({{ $notif->id }}, this)" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="text-slate-400 font-medium">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500 font-medium">
                    Menampilkan {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} dari {{ $notifications->total() }}
                </div>
                <div class="flex items-center gap-2">
                    @if (!$notifications->onFirstPage())
                        <a href="{{ $notifications->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50">← Prev</a>
                    @endif
                    <span class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-sm font-bold">{{ $notifications->currentPage() }}</span>
                    @if ($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50">Next →</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function markAsRead(id, btn) {
    fetch('{{ role_route("admin.notifications.read", ["notification" => 0]) }}'.replace('/0', '/' + id), {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        credentials: 'same-origin'
    }).then(() => window.location.reload());
}

function markAllRead(btn) {
    fetch('{{ role_route("admin.notifications.read-all") }}', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        credentials: 'same-origin'
    }).then(() => window.location.reload());
}

function deleteNotification(id, btn) {
    Swal.fire({
        title: 'Hapus notifikasi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl' }
    }).then((result) => {
        if (!result.isConfirmed) return;
        fetch('{{ role_route("admin.notifications.destroy", ["notification" => 0]) }}'.replace('/0', '/' + id), {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            credentials: 'same-origin'
        }).then(() => {
            const row = btn.closest('.flex.items-start');
            if (row) { row.style.opacity = '0'; row.style.transform = 'translateX(100%)'; row.style.transition = 'all 0.3s'; setTimeout(() => row.remove(), 300); }
        });
    });
}
</script>
@endpush
@endsection