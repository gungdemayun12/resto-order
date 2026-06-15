@extends('layouts.customer')
@section('title', 'Reservasi Meja')

@section('content')
<div x-data="reservationApp()" class="min-h-screen bg-gradient-to-br from-slate-50 to-amber-50/30">
    
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-lg mx-auto px-4 py-4 flex flex-col items-center gap-1">
            <x-resto-logo size="sm" />
            <p class="text-sm text-slate-500">Reservasi Meja</p>
        </div>
    </header>

    <div class="max-w-lg mx-auto px-4 py-6 space-y-6">
        
        <div x-show="successData" x-transition class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center" x-cloak>
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-lg font-bold text-emerald-800 mb-1">Reservasi Terkirim!</h3>
            <p class="text-sm text-emerald-600 mb-3">Kode reservasi Anda:</p>
            <p class="text-2xl font-bold text-emerald-700 mb-4" x-text="successData?.reservation?.reservation_code"></p>
            <p class="text-xs text-emerald-500">Kami akan mengkonfirmasi reservasi Anda segera. Simpan kode ini.</p>
            <button @click="successData = null" class="mt-4 text-sm text-emerald-600 underline">Buat Reservasi Lagi</button>
        </div>

        
        <div x-show="!successData" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5">Buat Reservasi</h2>
            <form @submit.prevent="submitReservation()" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" x-model="form.name" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm" placeholder="Masukkan nama Anda">
                    <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-xs mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Telepon *</label>
                    <input type="tel" x-model="form.phone" required pattern="[0-9]{10,15}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm" placeholder="08xxxxxxxxxx">
                    <p x-show="errors.phone" x-text="errors.phone" class="text-red-500 text-xs mt-1"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal *</label>
                        <div class="relative">
                            <input type="text" id="date-picker" required placeholder="Pilih tanggal" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            <svg class="w-4 h-4 absolute right-3 top-3 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Waktu *</label>
                        <select x-model="form.time" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="">Pilih</option>
                            @for($h = 10; $h <= 21; $h++)
                            <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                            <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Tamu *</label>
                    <div class="flex items-center gap-4">
                        <button type="button" @click="form.guests = Math.max(1, form.guests - 1)" class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg font-bold hover:bg-slate-200">-</button>
                        <span class="text-2xl font-bold text-slate-800 w-12 text-center" x-text="form.guests"></span>
                        <button type="button" @click="form.guests = Math.min(20, form.guests + 1)" class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center text-lg font-bold hover:bg-amber-200">+</button>
                        <span class="text-sm text-slate-400">orang</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                    <textarea x-model="form.notes" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm" placeholder="Permintaan khusus (opsional)"></textarea>
                </div>
                <button type="submit" :disabled="submitting" class="w-full py-3.5 bg-amber-600 text-white font-bold rounded-2xl hover:bg-amber-700 transition-colors shadow-lg shadow-amber-600/30 disabled:opacity-50">
                    <span x-show="!submitting">📅 Buat Reservasi</span>
                    <span x-show="submitting">Mengirim...</span>
                </button>
            </form>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Cek Status Reservasi</h2>
            <div class="flex gap-2">
                <input type="tel" x-model="checkPhone" placeholder="Masukkan nomor telepon" class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                <button @click="checkStatus()" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700">Cek</button>
            </div>
            <div x-show="checkResults !== null" class="mt-4 space-y-3" x-cloak>
                <template x-if="checkResults && checkResults.length === 0">
                    <p class="text-sm text-slate-400 text-center py-4">Tidak ditemukan reservasi aktif</p>
                </template>
                <template x-for="r in checkResults" :key="r.id">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-sm text-slate-800" x-text="r.reservation_code"></span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full" :class="{'bg-yellow-100 text-yellow-700': r.status==='pending', 'bg-emerald-100 text-emerald-700': r.status==='confirmed', 'bg-red-100 text-red-700': r.status==='rejected'}" x-text="r.status_label"></span>
                        </div>
                        <p class="text-xs text-slate-500" x-text="r.date + ' · ' + r.time + ' · ' + r.guests + ' tamu'"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function reservationApp() {
    return {
        form: { name: '', phone: '', date: '', time: '', guests: 2, notes: '' },
        errors: {}, submitting: false, successData: null,
        checkPhone: '', checkResults: null,
        today: new Date().toISOString().split('T')[0],

        async submitReservation() {
            this.errors = {};
            this.submitting = true;
            try {
                const res = await fetch('/reservasi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.successData = data;
                    this.form = { name: '', phone: '', date: '', time: '', guests: 2, notes: '' };
                } else if (data.errors) {
                    this.errors = {};
                    for (let k in data.errors) this.errors[k] = data.errors[k][0];
                    showToast('Periksa kembali data Anda', 'error');
                }
            } catch(e) { showToast('Terjadi kesalahan', 'error'); }
            this.submitting = false;
        },

        async checkStatus() {
            if (!this.checkPhone) return;
            try {
                const res = await fetch('/reservasi/check?phone=' + encodeURIComponent(this.checkPhone));
                const data = await res.json();
                this.checkResults = data.reservations || [];
            } catch(e) { showToast('Gagal mengecek status', 'error'); }
        },

        init() {
            flatpickr("#date-picker", {
                locale: "id",
                minDate: "today",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                onChange: (selectedDates, dateStr) => {
                    this.form.date = dateStr;
                }
            });
        }
    };
}
</script>
@endpush
@endsection
