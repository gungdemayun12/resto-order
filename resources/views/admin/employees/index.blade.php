@extends('layouts.admin')
@section('title', 'Manajemen Karyawan')
@section('subtitle', 'Kelola tim restoran Anda — tambah, ubah, dan atur hak akses karyawan')

@section('content')
<div x-data="{ showAddForm: false, editingId: null, searchQuery: '{{ request("search") }}', roleFilter: '{{ request("role", "all") }}' }" class="space-y-6">

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer" @click="roleFilter = 'all'; document.getElementById('role-filter').value = 'all'; document.getElementById('emp-filter-form').submit()">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-slate-100 rounded-full opacity-50 group-hover:bg-amber-100 transition-colors"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Karyawan</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $roleStats['all'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-amber-100 rounded-full opacity-50"></div>
            <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em]">Pemilik</p>
            <p class="text-3xl font-black text-amber-600 mt-1">{{ $roleStats['owner'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-blue-100 rounded-full opacity-50"></div>
            <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Kasir</p>
            <p class="text-3xl font-black text-blue-600 mt-1">{{ $roleStats['cashier'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
            <div class="absolute -right-3 -top-3 w-16 h-16 bg-emerald-100 rounded-full opacity-50"></div>
            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em]">Dapur</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">{{ $roleStats['kitchen'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1">
                <form id="emp-filter-form" method="GET" action="{{ role_route('admin.employees.index') }}" class="flex items-center gap-3 flex-1 flex-wrap">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:bg-white transition-all outline-none">
                    </div>
                    <select name="role" id="role-filter" class="px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-amber-500/20 cursor-pointer outline-none">
                        <option value="all" {{ request('role', 'all') == 'all' ? 'selected' : '' }}>Semua Peran</option>
                        <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Pemilik</option>
                        <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Kasir</option>
                        <option value="kitchen" {{ request('role') == 'kitchen' ? 'selected' : '' }}>Dapur</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all shadow-sm active:scale-95">Filter</button>
                    @if(request()->anyFilled(['search', 'role']) && request('role', 'all') !== 'all')
                        <a href="{{ role_route('admin.employees.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-400 hover:text-red-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-50 transition-all">Reset</a>
                    @endif
                </form>
            </div>
            <button @click="showAddForm = !showAddForm; editingId = null"
                class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-all shadow-sm shadow-amber-500/20 active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Karyawan
            </button>
        </div>
    </div>

    <div x-show="showAddForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-800">Tambah Karyawan Baru</h3>
                <button @click="showAddForm = false" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ role_route('admin.employees.store') }}" id="add-employee-form" class="employee-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Masukkan nama lengkap"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                        <input type="email" name="email" required placeholder="email@contoh.com"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Peran</label>
                        <select name="role" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none cursor-pointer transition-all">
                            <option value="cashier">Kasir</option>
                            <option value="kitchen">Dapur</option>
                            <option value="owner">Pemilik (Owner)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Izin Akses</label>
                        <p class="text-xs text-slate-400 mt-1">Izin akan otomatis mengikuti peran yang dipilih. Anda bisa mengubahnya nanti di Pengaturan Akses.</p>
                    </div>
                </div>
                <div class="mt-5 flex items-center gap-3 justify-end">
                    <button type="button" @click="showAddForm = false" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-all shadow-sm shadow-amber-500/20 active:scale-95">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="pl-8 pr-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Karyawan</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Email</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Peran</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Izin Aktif</th>
                        <th class="pl-4 pr-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($employees as $employee)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="pl-8 pr-4 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm"
                                        style="background: {{ $employee->role === 'owner' ? '#f59e0b' : ($employee->role === 'cashier' ? '#3b82f6' : '#10b981') }}">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-amber-600 transition-colors">{{ $employee->name }}</p>
                                        @if($employee->id === auth()->id())
                                            <span class="text-[9px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full uppercase tracking-wider">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-sm text-slate-500 font-medium">{{ $employee->email }}</td>
                            <td class="px-4 py-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black rounded-full uppercase tracking-widest
                                    {{ $employee->role === 'owner' ? 'bg-amber-100 text-amber-700' : ($employee->role === 'cashier' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $employee->role === 'owner' ? 'bg-amber-500' : ($employee->role === 'cashier' ? 'bg-blue-500' : 'bg-emerald-500') }}"></span>
                                    {{ $employee->role_label }}
                                </span>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @foreach($employee->effective_permissions as $perm)
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-bold rounded-full">{{ ucfirst(str_replace('_', ' ', $perm)) }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="pl-4 pr-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="editingId = {{ $employee->id }}; showAddForm = false"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[11px] font-bold hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    @if($employee->id !== auth()->id())
                                        <button onclick="confirmDeleteEmployee({{ $employee->id }}, '{{ $employee->name }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 text-slate-400 rounded-xl text-[11px] font-bold hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <tr x-show="editingId === {{ $employee->id }}" x-transition class="bg-amber-50/30">
                            <td colspan="5" class="px-8 py-6">
                                <form method="POST" action="{{ role_route('admin.employees.update', $employee) }}" class="employee-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ $employee->name }}" required
                                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                                            <input type="email" name="email" value="{{ $employee->email }}" required
                                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password Baru <span class="text-slate-300 normal-case">(opsional)</span></label>
                                            <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                                            <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Peran</label>
                                            <select name="role" required class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none cursor-pointer transition-all">
                                                <option value="owner" {{ $employee->role === 'owner' ? 'selected' : '' }}>Pemilik (Owner)</option>
                                                <option value="cashier" {{ $employee->role === 'cashier' ? 'selected' : '' }}>Kasir</option>
                                                <option value="kitchen" {{ $employee->role === 'kitchen' ? 'selected' : '' }}>Dapur</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Izin Akses</label>
                                            <div class="flex flex-wrap gap-2">
                                                @php $allPerms = \App\Models\User::defaultPermissions('owner'); @endphp
                                                @foreach($allPerms as $perm)
                                                    <label class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl cursor-pointer hover:bg-amber-50 hover:border-amber-200 transition-all text-xs font-medium text-slate-700">
                                                        <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                                            {{ in_array($perm, $employee->effective_permissions) ? 'checked' : '' }}
                                                            class="w-3.5 h-3.5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                                        {{ ucfirst(str_replace('_', ' ', $perm)) }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5 flex items-center gap-3 justify-end">
                                        <button type="button" @click="editingId = null" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all">Batal</button>
                                        <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-all shadow-sm shadow-amber-500/20 active:scale-95">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-800">{{ $employees->firstItem() }}</span> sampai <span class="font-bold text-slate-800">{{ $employees->lastItem() }}</span> dari <span class="font-bold text-slate-800">{{ $employees->total() }}</span> karyawan
                </div>
                <div class="flex items-center gap-2">
                    @if ($employees->onFirstPage())
                        <span class="px-3 py-2 bg-slate-200 text-slate-400 rounded-xl text-sm font-medium cursor-not-allowed">← Prev</span>
                    @else
                        <a href="{{ $employees->previousPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">← Prev</a>
                    @endif
                    <span class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold shadow-sm">{{ $employees->currentPage() }} / {{ $employees->lastPage() }}</span>
                    @if ($employees->hasMorePages())
                        <a href="{{ $employees->nextPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all">Next →</a>
                    @else
                        <span class="px-3 py-2 bg-slate-200 text-slate-400 rounded-xl text-sm font-medium cursor-not-allowed">Next →</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function confirmDeleteEmployee(id, name) {
    Swal.fire({
        title: 'Hapus Karyawan?',
        text: 'Anda yakin ingin menghapus "' + name + '"? Aksi ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl' }
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        try {
            const response = await fetch('{{ role_route("admin.employees.destroy", ["employee" => 0]) }}'.replace('/0', '/' + id), {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (response.ok && data.success) {
                await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1800, timerProgressBar: true, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                window.location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menghapus karyawan.', customClass: { popup: 'rounded-2xl' } });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Koneksi Bermasalah', text: 'Periksa jaringan lalu coba lagi.', customClass: { popup: 'rounded-2xl' } });
        }
    });
}

document.querySelectorAll('.employee-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Data?',
            text: 'Pastikan semua data sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: form.method === 'post' ? 'POST' : (form.querySelector('input[name="_method"]')?.value || form.method).toUpperCase() === 'PUT' ? 'POST' : 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                const data = await response.json();
                if (response.ok && data.success !== false) {
                    await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message || 'Data berhasil disimpan!', timer: 1800, timerProgressBar: true, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                    window.location.reload();
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat().join('\n') : '';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || errors || 'Gagal menyimpan data.', customClass: { popup: 'rounded-2xl' } });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Koneksi Bermasalah', text: 'Periksa jaringan lalu coba lagi.', customClass: { popup: 'rounded-2xl' } });
            }
        });
    });
});
</script>
@endpush
@endsection
