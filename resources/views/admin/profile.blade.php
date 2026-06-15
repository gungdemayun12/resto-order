@extends('layouts.admin')
@section('title', 'Profil Saya')
@section('subtitle', 'Kelola informasi akun dan keamanan Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-100/40 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
        <div class="relative flex items-center gap-6">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg"
                style="background: linear-gradient(135deg, #f59e0b, #d97706)">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800">{{ auth()->user()->name }}</h2>
                <p class="text-slate-500 font-medium mt-0.5">{{ auth()->user()->email }}</p>
                <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-widest
                    {{ auth()->user()->role === 'owner' ? 'bg-amber-100 text-amber-700' : (auth()->user()->role === 'cashier' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->role === 'owner' ? 'bg-amber-500' : (auth()->user()->role === 'cashier' ? 'bg-blue-500' : 'bg-emerald-500') }}"></span>
                    {{ auth()->user()->role_label }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Informasi Profil</h3>
                    <p class="text-xs text-slate-400">Perbarui nama dan email Anda</p>
                </div>
            </div>
            <form method="POST" action="{{ role_route('admin.profile.update') }}" id="profile-form">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="w-full px-6 py-3 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-all shadow-sm shadow-amber-500/20 active:scale-95">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Ubah Password</h3>
                    <p class="text-xs text-slate-400">Perbarui keamanan akun Anda</p>
                </div>
            </div>
            <form method="POST" action="{{ role_route('admin.profile.password') }}" id="password-form">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password Lama</label>
                        <input type="password" name="current_password" required placeholder="Masukkan password lama"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password Baru</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password baru"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="w-full px-6 py-3 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-all shadow-sm active:scale-95">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('#profile-form, #password-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Perubahan?',
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
                    method: 'POST',
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