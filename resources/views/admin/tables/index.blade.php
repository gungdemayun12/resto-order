@extends('layouts.admin')
@section('title', 'Meja & QR Digital')
@section('subtitle', 'Kendali penuh atas tata letak dan akses pelanggan')

@section('content')
<div x-data="tableManager()" class="space-y-10">
    
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex flex-col md:flex-row items-center gap-10">
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Tersedia</p>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ $tables->where('status', 'available')->count() }} <span class="text-sm font-bold text-slate-400 uppercase">Meja</span></p>
                </div>
            </div>
            <div class="w-px h-10 bg-slate-100 hidden md:block"></div>
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Terisi</p>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ $tables->where('status', 'occupied')->count() }} <span class="text-sm font-bold text-slate-400 uppercase">Meja</span></p>
                </div>
            </div>
        </div>
        
        <button @click="resetTableForm(); openModal = true" class="w-full lg:w-auto px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-2xl shadow-slate-900/20 flex items-center justify-center gap-3 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Meja Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="GET" action="{{ role_route('admin.tables.index') }}" class="admin-live-search flex flex-col md:flex-row gap-4 items-center justify-between" data-live-search="tables" data-live-target="#tables-list">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="flex flex-1 w-full gap-3">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor meja atau lokasi..." autocomplete="off" class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-medium focus:ring-4 focus:ring-emerald-500/10 shadow-inner">
                </div>
                <div class="w-48">
                    <select name="capacity" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-4 focus:ring-emerald-500/10 shadow-inner outline-none cursor-pointer">
                        <option value="">Semua Kapasitas</option>
                        @foreach([2, 4, 6, 8, 10] as $cap)<option value="{{ $cap }}" {{ request('capacity') == $cap ? 'selected' : '' }}>{{ $cap }} Tamu</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-4 md:pt-0 md:border-0 border-t border-slate-100 w-full md:w-auto">
                <a href="{{ role_route('admin.tables.index', ['search' => request('search'), 'capacity' => request('capacity'), 'status' => '']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ !request('status') ? 'bg-emerald-500 text-white shadow-lg' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                <a href="{{ role_route('admin.tables.index', ['search' => request('search'), 'capacity' => request('capacity'), 'status' => 'available']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'available' ? 'bg-emerald-500 text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Tersedia</a>
                <a href="{{ role_route('admin.tables.index', ['search' => request('search'), 'capacity' => request('capacity'), 'status' => 'occupied']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'occupied' ? 'bg-red-500 text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Terisi</a>
                <a href="{{ role_route('admin.tables.index', ['search' => request('search'), 'capacity' => request('capacity'), 'status' => 'reserved']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'reserved' ? 'bg-amber-500 text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Dipesan</a>
            </div>
        </form>
    </div>

    <div id="tables-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($tables as $table)
        @php
            $statusConfig = [
                'available' => ['border' => 'border-emerald-100', 'bg' => 'bg-emerald-50/30', 'dot' => 'bg-emerald-500', 'shadow' => 'hover:shadow-emerald-500/10', 'text' => 'text-emerald-600'],
                'occupied' => ['border' => 'border-red-100', 'bg' => 'bg-red-50/30', 'dot' => 'bg-red-500', 'shadow' => 'hover:shadow-red-500/10', 'text' => 'text-red-600'],
                'reserved' => ['border' => 'border-amber-100', 'bg' => 'bg-amber-50/30', 'dot' => 'bg-amber-500', 'shadow' => 'hover:shadow-amber-500/10', 'text' => 'text-amber-600'],
            ];
            $cfg = $statusConfig[$table->status] ?? ['border' => 'border-slate-100', 'bg' => 'bg-slate-50/30', 'dot' => 'bg-slate-500', 'shadow' => '', 'text' => 'text-slate-600'];
            $locIcon = match($table->location) { 'indoor' => '🏠', 'outdoor' => '🌳', 'vip' => '💎', default => '📍' };
        @endphp
        <div class="group relative bg-white rounded-2xl border-2 {{ $cfg['border'] }} p-2 hover:shadow-2xl {{ $cfg['shadow'] }} transition-all duration-700 transform hover:-translate-y-2 overflow-hidden">
            <button type="button" @click='openEditModal(@json($table))'
                    class="absolute top-4 right-4 z-20 px-3 py-2 bg-white/90 text-slate-700 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl border border-slate-200 shadow-sm hover:bg-slate-100 transition-all">
                Edit
            </button>
            <div class="{{ $cfg['bg'] }} rounded-2xl p-8 h-full flex flex-col relative z-10">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} {{ $table->status !== 'available' ? 'animate-pulse' : '' }}"></span>
                            <span class="text-[9px] font-black {{ $cfg['text'] }} uppercase tracking-[0.2em]">{{ $table->status_label }}</span>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tighter">Meja {{ $table->number }}</h3>
                    </div>
                    <div class="px-4 py-2 bg-white/80 backdrop-blur-md border border-white shadow-sm rounded-2xl text-[10px] font-black text-slate-800 uppercase tracking-widest">
                        {{ $locIcon }} {{ $table->location_label }}
                    </div>
                </div>

                <div class="space-y-4 mb-10 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">👤 Kapasitas</span>
                        <span class="text-xs font-black text-slate-800">{{ $table->capacity }} Tamu</span>
                    </div>
                    
                    @php $session = $table->activeSession; @endphp
                    @if($session)
                    <div class="p-5 bg-white rounded-2xl border border-slate-50 shadow-xl shadow-slate-900/5 mt-6 relative overflow-hidden group/session">
                        <div class="absolute top-0 right-0 p-2 opacity-[0.03]">
                            <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em]">Sesi Aktif</p>
                            <span class="text-[9px] font-bold text-slate-400">Exp: {{ $session->expires_at->format('H:i') }}</span>
                        </div>
                        <p class="text-xs font-black text-slate-800 truncate mb-4">{{ $session->orders->first()->customer_name ?? 'Pelanggan' }}</p>
                        <button @click="closeSession({{ $session->id }}, '{{ $table->number }}')" 
                                class="w-full py-3 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-600 hover:text-white transition-all active:scale-95">
                            Tutup Sesi
                        </button>
                    </div>
                    @else
                    <div class="h-16 flex flex-col items-center justify-center border-2 border-dashed border-slate-200/50 rounded-2xl mt-6">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.3em]">Meja Kosong</span>
                    </div>
                    @endif
                </div>

                <div class="mt-auto grid grid-cols-5 gap-3">
                    <button @click="showQr('{{ route('qr.scan', ['qrToken' => $table->qr_token]) }}', {{ $table->number }})" 
                            class="col-span-2 py-4 bg-white text-slate-800 border border-slate-100 rounded-2xl hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </button>
                    
                    <div class="col-span-3 relative">
                        <form method="POST" action="{{ role_route('admin.tables.updateStatus', $table) }}">
                            @csrf @method('PATCH')
                            <select name="status" onchange="confirmStatusChange(this)" 
                                    class="w-full pl-6 pr-10 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest appearance-none cursor-pointer focus:ring-4 focus:ring-amber-500/20 transition-all shadow-xl shadow-slate-900/10">
                                <option value="available" {{ $table->status=='available'?'selected':'' }}>Tersedia</option>
                                <option value="occupied" {{ $table->status=='occupied'?'selected':'' }}>Terisi</option>
                                <option value="reserved" {{ $table->status=='reserved'?'selected':'' }}>Dipesan</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-slate-100/50 rounded-full blur-3xl -z-0"></div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <div class="w-24 h-24 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h4 class="text-lg font-black text-slate-800">Tidak ada meja yang cocok</h4>
            <p class="text-sm text-slate-500 mt-2">Coba ubah kata kunci, kapasitas, atau status filter.</p>
        </div>
        @endforelse
    </div>

    <div x-show="openModal" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-sm" x-cloak>
        
        <div @click.away="!submitting && (openModal = false)" 
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-8 scale-95" x-transition:enter-end="translate-y-0 scale-100">
            
            <div class="px-10 py-8 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight" x-text="editingTableId ? 'Edit Meja' : 'Register Meja'"></h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1" x-text="editingTableId ? 'Perbarui detail kategori dan kapasitas meja' : 'Tambahkan unit meja baru ke sistem'"></p>
                </div>
                <button @click="openModal = false" class="w-10 h-10 flex items-center justify-center bg-white rounded-2xl text-slate-400 hover:text-slate-600 shadow-sm border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="table-form" @submit.prevent="handleStoreTable" class="p-10 space-y-8 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nomor Meja</label>
                        <input type="number" name="number" x-model="tableForm.number" required min="1" placeholder="Ex: 10"
                               :class="errors.number ? 'ring-2 ring-red-500 bg-red-50' : 'bg-slate-100 focus:bg-white'"
                               class="w-full px-6 py-4 border-none rounded-2xl text-sm font-black text-slate-800 transition-all shadow-inner focus:ring-4 focus:ring-amber-500/10 outline-none">
                        <p x-show="errors.number" x-text="errors.number[0]" class="text-red-500 text-[10px] font-bold mt-2 ml-1"></p>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Kapasitas</label>
                        <input type="number" name="capacity" x-model="tableForm.capacity" required min="1" max="20"
                               :class="errors.capacity ? 'ring-2 ring-red-500 bg-red-50' : 'bg-slate-100 focus:bg-white'"
                               class="w-full px-6 py-4 border-none rounded-2xl text-sm font-black text-slate-800 transition-all shadow-inner focus:ring-4 focus:ring-amber-500/10 outline-none">
                        <p x-show="errors.capacity" x-text="errors.capacity[0]" class="text-red-500 text-[10px] font-bold mt-2 ml-1"></p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Zona Lokasi</label>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach(['indoor' => '🏠 Indoor', 'outdoor' => '🌳 Outdoor', 'vip' => '💎 VIP'] as $val => $label)
                        <label class="relative group cursor-pointer">
                            <input type="radio" name="location" value="{{ $val }}" x-model="tableForm.location" class="peer sr-only">
                            <div class="px-2 py-4 bg-slate-100 border-none rounded-2xl text-center text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:bg-slate-900 peer-checked:text-white shadow-inner transition-all hover:bg-slate-200">
                                {{ $label }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p x-show="errors.location" x-text="errors.location[0]" class="text-red-500 text-[10px] font-bold mt-2 ml-1"></p>
                </div>

                <button type="submit" :disabled="submitting"
                        class="w-full py-5 bg-amber-500 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/20 flex items-center justify-center gap-3 active:scale-95">
                    <span x-text="submitting ? (editingTableId ? 'Menyimpan...' : 'Mendaftarkan...') : (editingTableId ? 'Perbarui Meja' : 'Daftarkan Meja')"></span>
                    <template x-if="submitting">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                </button>
            </form>
        </div>
    </div>

    <div x-show="openQrModal" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/90 backdrop-blur-md" x-cloak>
        <div @click.away="openQrModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center transform transition-all" x-transition:enter="ease-out duration-300" x-transition:enter-start="scale-90 opacity-0">
            <div class="p-12">
                <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <h3 class="text-3xl font-black text-slate-900 tracking-tighter mb-2">Meja #<span x-text="qrTableNumber"></span></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-10">Scan untuk akses Menu Digital</p>
                
                <div class="bg-white p-6 rounded-2xl border-4 border-slate-50 inline-block mb-10 shadow-2xl shadow-slate-200">
                    <div id="qr-container" class="bg-white"></div>
                </div>
                
                <div class="flex flex-col gap-3">
                    <button @click="downloadQr()" class="w-full py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10">Download PNG</button>
                    <button @click="openQrModal = false" class="w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 rounded-2xl hover:bg-slate-50 transition-all">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
function tableManager() {
    return {
        openModal: false,
        openQrModal: false,
        qrTableNumber: null,
        submitting: false,
        tableForm: { number: '', capacity: 4, location: 'indoor' },
        errors: {},
        
        showQr(url, number) {
            this.qrTableNumber = number;
            this.openQrModal = true;
            
            setTimeout(() => {
                const container = document.getElementById('qr-container');
                container.innerHTML = '';
                new QRCode(container, {
                    text: url,
                    width: 240,
                    height: 240,
                    colorDark: "#0f172a",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }, 100);
        },

        downloadQr() {
            const canvas = document.querySelector('#qr-container canvas');
            if (canvas) {
                const link = document.createElement('a');
                link.download = `QR_RestoNusa_Meja_${this.qrTableNumber}.png`;
                link.href = canvas.toDataURL();
                link.click();
            }
        },

        async handleStoreTable(e) {
            this.submitting = true;
            this.errors = {};
            const url = this.updateUrl || '{{ role_route('admin.tables.store') }}';
            const method = this.updateUrl ? 'PATCH' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.tableForm)
                });

                const result = await response.json();

                if (response.ok) {
                    showToast(this.updateUrl ? 'Meja berhasil diperbarui' : 'Meja berhasil didaftarkan');
                    this.openModal = false;
                    setTimeout(() => location.reload(), 1000);
                } else if (response.status === 422) {
                    this.errors = result.errors;
                    showToast('Cek rincian meja Anda', 'error');
                }
            } catch (e) {
                showToast('Kesalahan koneksi', 'error');
            } finally {
                this.submitting = false;
            }
        },

        resetTableForm() {
            this.editingTableId = null;
            this.updateUrl = null;
            this.tableForm = { number: '', capacity: 4, location: 'indoor' };
            this.errors = {};
        },

        openEditModal(table) {
            this.editingTableId = table.id;
            this.updateUrl = '/{{ role_prefix() }}/tables/' + table.id;
            this.tableForm = { number: table.number, capacity: table.capacity, location: table.location };
            this.errors = {};
            this.openModal = true;
        },

        async closeSession(id, tableNumber) {
            const confirm = await Swal.fire({
                title: `Tutup Sesi Meja #${tableNumber}?`,
                text: "Sesi akan berakhir dan data pelanggan akan diarsipkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                confirmButtonText: 'Ya, Tutup Sesi!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            });

            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Menutup sesi...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch('/{{ role_prefix() }}/sessions/' + id + '/close', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                        'Accept': 'application/json' 
                    }
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Sesi meja berhasil ditutup.',
                        timer: 1800,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' }
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menutup Sesi',
                        text: data.message || 'Tidak dapat menutup sesi meja.',
                        confirmButtonColor: '#0f172a',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Bermasalah',
                    text: 'Terjadi kesalahan saat menutup sesi. Periksa jaringan lalu coba lagi.',
                    confirmButtonColor: '#0f172a',
                    customClass: { popup: 'rounded-2xl' }
                });
            }
        }
    }
}

function confirmStatusChange(select) {
    const card = select.closest('.group');
    const tableNum = card ? (card.querySelector('h3')?.innerText || 'ini') : 'ini';
    const statusLabels = { available: 'Tersedia', occupied: 'Terisi', reserved: 'Dipesan' };
    const newLabel = statusLabels[select.value] || select.value;

    Swal.fire({
        title: 'Ubah Status Meja?',
        text: `Meja ${tableNum} akan diatur menjadi "${newLabel}".`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        confirmButtonText: 'Ya, Update!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl' }
    }).then(async (result) => {
        if (!result.isConfirmed) {
            // Kembalikan pilihan ke nilai lama
            location.reload();
            return;
        }

        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const form = select.form;
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

            // Controller bisa redirect atau JSON — tangani keduanya
            const data = await response.json().catch(() => ({ success: true }));

            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || `Status meja berhasil diubah ke "${newLabel}".`,
                timer: 1800,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' }
            });
            location.reload();
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan. Coba lagi.',
                customClass: { popup: 'rounded-2xl' }
            });
        }
    });
}
</script>
@endpush
@endsection
