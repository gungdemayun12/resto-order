@extends('layouts.admin')
@section('title', 'Pengaturan Hak Akses')
@section('subtitle', 'Kelola peran dan izin akses untuk setiap pengguna')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4a4 4 0 110 8 4 4 0 010-8zm-7 16a7 7 0 0114 0H5z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Pengaturan Hak Akses</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pemilik dapat mengubah peran dan izin setiap pengguna.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="pl-8 pr-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nama</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Email</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Peran</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Izin Aktif</th>
                        <th class="pl-4 pr-8 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="pl-8 pr-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm"
                                    style="background: {{ $user->role === 'owner' ? '#f59e0b' : ($user->role === 'cashier' ? '#3b82f6' : '#10b981') }}">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-slate-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-slate-500 font-medium">{{ $user->email }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-widest
                                {{ $user->role === 'owner' ? 'bg-amber-100 text-amber-700' : ($user->role === 'cashier' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->role === 'owner' ? 'bg-amber-500' : ($user->role === 'cashier' ? 'bg-blue-500' : 'bg-emerald-500') }}"></span>
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->effective_permissions as $perm)
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-bold rounded-full">{{ ucfirst(str_replace('_', ' ', $perm)) }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="pl-4 pr-8 py-4 text-right">
                            <button onclick="toggleEdit({{ $user->id }})"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Ubah
                            </button>
                        </td>
                    </tr>
                    <tr id="edit-user-{{ $user->id }}" class="hidden">
                        <td colspan="5" class="px-6 py-6">
                            <form method="POST" action="{{ role_route('admin.settings.update', $user) }}" class="settings-update-form">
                                @csrf
                                @method('PATCH')
                                <div class="bg-amber-50/30 border border-amber-100 rounded-2xl p-6 space-y-5">
                                    <div class="grid gap-5 md:grid-cols-2">

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2">Peran</label>
                                            <select name="role" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all shadow-sm">
                                                <option value="owner"   {{ $user->role === 'owner'   ? 'selected' : '' }}>👑 Pemilik (Owner)</option>
                                                <option value="cashier" {{ $user->role === 'cashier' ? 'selected' : '' }}>💰 Kasir</option>
                                                <option value="kitchen" {{ $user->role === 'kitchen' ? 'selected' : '' }}>🍳 Dapur</option>
                                            </select>
                                            <p class="mt-2 text-[10px] text-slate-400 font-medium">URL: owner → /admin, kasir → /kasir, dapur → /dapur</p>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2">Izin Akses</label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($availablePermissions as $permission)
                                                <label class="relative flex items-center cursor-pointer group">
                                                    <input type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission }}"
                                                        {{ in_array($permission, $user->effective_permissions) ? 'checked' : '' }}
                                                        class="peer sr-only">
                                                    <div class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 transition-all hover:border-amber-300 shadow-sm">
                                                        {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                                    </div>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-3 justify-end pt-2 border-t border-amber-100">
                                        <button type="button" onclick="toggleEdit({{ $user->id }})"
                                            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="rounded-xl bg-amber-500 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-amber-500/20 hover:bg-amber-600 transition-all active:scale-95">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Intercept settings form dengan SweetAlert ──
    document.querySelectorAll('form.settings-update-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: 'Hak akses pengguna ini akan diperbarui.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                // Loading state
                Swal.fire({
                    title: 'Menyimpan...',
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

                    const data = await response.json().catch(() => ({ success: response.ok }));

                    if (response.ok && data.success !== false) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Hak akses pengguna berhasil diperbarui.',
                            timer: 1800,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-2xl' }
                        });
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Terjadi kesalahan saat menyimpan.',
                            customClass: { popup: 'rounded-2xl' }
                        });
                    }
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Bermasalah',
                        text: 'Periksa jaringan lalu coba lagi.',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            });
        });
    });
});

function toggleEdit(userId) {
    const row = document.getElementById('edit-user-' + userId);
    if (row) row.classList.toggle('hidden');
}
</script>
@endpush
@endsection
