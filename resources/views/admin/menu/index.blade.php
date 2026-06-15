@extends('layouts.admin')
@section('title', 'Manajemen Menu')
@section('subtitle', 'Kelola koleksi hidangan terbaik Anda')

@section('content')
<div x-data="menuManager()" class="space-y-8">
    
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 space-y-4">
        <form method="GET" action="{{ role_route('admin.menu.index') }}" class="admin-live-search flex flex-col md:flex-row gap-4 items-center justify-between" data-live-search="menu" data-live-target="#menu-items-list">
            <div class="flex flex-1 w-full gap-3">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..." autocomplete="off" class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-semibold focus:ring-4 focus:ring-amber-500/10 focus:bg-white transition-all shadow-inner">
                </div>

                <div class="w-48">
                    <select name="sort" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-4 focus:ring-amber-500/10 shadow-inner outline-none cursor-pointer">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Urut: Terbaru</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Terendah</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tertinggi</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                    </select>
                </div>
            </div>
            
            <button @click="openCreateModal()" class="w-full md:w-auto px-8 py-3 bg-slate-900 text-white rounded-xl font-black text-sm uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 flex items-center justify-center gap-3 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Menu
            </button>
        </form>

        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Filter Status:</span>
            <form method="GET" action="{{ role_route('admin.menu.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <a href="{{ role_route('admin.menu.index', ['search' => request('search'), 'category' => request('category'), 'sort' => request('sort')]) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ !request('status') || request('status') === 'all' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                <a href="{{ role_route('admin.menu.index', ['search' => request('search'), 'category' => request('category'), 'sort' => request('sort'), 'status' => 'available']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'available' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Tersedia</a>
                <a href="{{ role_route('admin.menu.index', ['search' => request('search'), 'category' => request('category'), 'sort' => request('sort'), 'status' => 'unavailable']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ request('status') === 'unavailable' ? 'bg-red-500 text-white shadow-lg shadow-red-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Habis</a>
            </form>
        </div>
    </div>

    <div class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-2">
        <a href="{{ role_route('admin.menu.index', ['search' => request('search'), 'sort' => request('sort'), 'status' => request('status')]) }}" 
           class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap {{ !request('category') ? 'bg-amber-500 text-white shadow-xl shadow-amber-500/20 ring-4 ring-amber-500/10' : 'bg-white text-slate-500 border border-slate-200 hover:border-amber-300 hover:text-amber-600 shadow-sm' }}">
           Semua Koleksi
        </a>
        @foreach($categories as $cat)
        <a href="{{ role_route('admin.menu.index', ['category' => $cat->id, 'search' => request('search'), 'sort' => request('sort'), 'status' => request('status')]) }}" 
           class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap {{ request('category') == $cat->id ? 'bg-amber-500 text-white shadow-xl shadow-amber-500/20 ring-4 ring-amber-500/10' : 'bg-white text-slate-500 border border-slate-200 hover:border-amber-300 hover:text-amber-600 shadow-sm' }}">
           {{ $cat->icon }} {{ $cat->name }}
        </a>
        @endforeach
    </div>

    <div id="menu-items-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($menuItems as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 group relative">
            <div class="relative aspect-[4/3] overflow-hidden bg-slate-50">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                @if(!$item->is_available)
                <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px] flex items-center justify-center">
                    <div class="relative flex items-center justify-center" style="transform: rotate(-15deg);">
                        <div class="border-4 border-red-600 rounded-sm px-4 py-2 shadow-xl"
                             style="box-shadow: 0 0 0 2px #dc2626 inset, 0 4px 16px rgba(220,38,38,0.4);">
                            <span class="text-red-600 font-black text-xl uppercase tracking-[0.25em] leading-none"
                                  style="text-shadow: 0 1px 0 rgba(0,0,0,0.3); letter-spacing: 0.3em;">SOLD OUT</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                    @foreach($item->labels ?? [] as $label)
                    @php $lc = ['best_seller'=>'bg-amber-500','recommended'=>'bg-blue-500','vegetarian'=>'bg-green-500','spicy'=>'bg-red-500','halal'=>'bg-emerald-600']; @endphp
                    <span class="px-2.5 py-1 text-xs font-semibold text-white rounded-xl shadow-lg {{ $lc[$label] ?? 'bg-slate-500' }} normal-case tracking-normal" style="text-transform:none;">{{ ucwords(strtolower(str_replace('_',' ',$label))) }}</span>
                    @endforeach
                </div>

                <div class="absolute bottom-4 right-4 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all translate-y-4 group-hover:translate-y-0">
                    <button @click="editItem({{ $item->toJson() }})" class="w-10 h-10 bg-white text-slate-800 rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button @click="confirmDelete({{ $item->id }})" class="w-10 h-10 bg-white text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-base font-black text-slate-800 leading-tight">{{ $item->name }}</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $item->category->name }}</p>
                    </div>
                    <p class="text-base font-black text-amber-600">{{ $item->formatted_price }}</p>
                </div>
                <p class="text-xs font-medium text-slate-500 line-clamp-2 mb-6 leading-relaxed">{{ $item->description }}</p>
                <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                    <span class="flex items-center gap-1.5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $item->estimated_time }} MNT
                    </span>
                    <button @click="toggleStatus({{ $item->id }})" 
                        :class="itemStatus[{{ $item->id }}] ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-400 border-slate-100'"
                        class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border transition-all">
                        <span x-text="itemStatus[{{ $item->id }}] ? 'Tersedia' : 'Habis'"></span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <div class="w-24 h-24 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h4 class="text-lg font-black text-slate-800">Koleksi Menu Kosong</h4>
            <p class="text-sm text-slate-500 mt-2">Mulai tambahkan menu lezat untuk pelanggan Anda</p>
        </div>
        @endforelse
    </div>

    <div x-show="openModal" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm" x-cloak>
        
        <div @click.outside="!submitting && (openModal = false)" 
             class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-8 scale-95" x-transition:enter-end="translate-y-0 scale-100">
            
            <div class="px-10 py-8 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight" x-text="editMode ? 'Edit Koleksi Menu' : 'Tambah Menu Baru'"></h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Lengkapi rincian hidangan di bawah ini</p>
                </div>
                <button @click="openModal = false" :disabled="submitting" class="w-10 h-10 flex items-center justify-center bg-white rounded-2xl text-slate-400 hover:text-slate-600 shadow-sm border border-slate-100 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="menu-form" @submit.prevent="handleSubmit" class="flex-1 overflow-y-auto no-scrollbar p-10 space-y-8" novalidate>
                <div class="grid grid-cols-1 gap-8">
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Nama Hidangan</label>
                        <input type="text" name="name" x-model="formData.name" placeholder="Misal: Nasi Goreng Spesial"
                               :class="errors.name ? 'ring-2 ring-red-500 bg-red-50' : 'bg-slate-50 focus:bg-white'"
                               class="w-full px-6 py-4 border-none rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-300 transition-all shadow-inner focus:ring-4 focus:ring-amber-500/10 outline-none">
                        <p x-show="errors.name" x-text="errors.name[0]" class="text-red-500 text-[10px] font-bold mt-2 ml-1 animate-pulse"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Kategori</label>
                            <select name="category_id" x-model="formData.category_id"
                                    :class="errors.category_id ? 'ring-2 ring-red-500 bg-red-50' : 'bg-slate-50 focus:bg-white'"
                                    class="w-full px-6 py-4 border-none rounded-2xl text-sm font-bold text-slate-800 transition-all shadow-inner focus:ring-4 focus:ring-amber-500/10 outline-none appearance-none">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                            </select>
                            <p x-show="errors.category_id" x-text="errors.category_id[0]" class="text-red-500 text-[10px] font-bold mt-2 ml-1"></p>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Harga (IDR)</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 font-black text-sm">Rp</span>
                                <input type="number" name="price" x-model="formData.price" placeholder="50000"
                                       :class="errors.price ? 'ring-2 ring-red-500 bg-red-50' : 'bg-slate-50 focus:bg-white'"
                                       class="w-full pl-14 pr-6 py-4 border-none rounded-2xl text-sm font-bold text-slate-800 transition-all shadow-inner focus:ring-4 focus:ring-amber-500/10 outline-none">
                            </div>
                            <p x-show="errors.price" x-text="errors.price[0]" class="text-red-500 text-[10px] font-bold mt-2 ml-1"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Deskripsi & Cerita Hidangan</label>
                        <textarea name="description" x-model="formData.description" rows="3" placeholder="Jelaskan keunikan rasa hidangan ini..."
                                  class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 transition-all shadow-inner focus:ring-4 focus:ring-amber-500/10 outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Waktu Saji (Mnt)</label>
                            <input type="number" name="estimated_time" x-model="formData.estimated_time"
                                   class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 transition-all shadow-inner focus:ring-4 focus:ring-amber-500/10 outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Foto Hidangan</label>
                            <input type="file" name="image" id="menu-image" accept="image/*" @change="previewImage"
                                   class="hidden">
                            <label for="menu-image" class="w-full px-6 py-4 bg-amber-50 border-2 border-dashed border-amber-200 rounded-2xl flex items-center justify-center gap-3 cursor-pointer hover:bg-amber-100 transition-all group">
                                <svg class="w-5 h-5 text-amber-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-black text-amber-700 uppercase tracking-widest" x-text="fileName || 'Upload Foto'"></span>
                            </label>
                            <p x-show="errors.image" x-text="errors.image[0]" class="text-red-500 text-[10px] font-bold mt-2 ml-1"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 ml-1">Berikan Label Khusus</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach(['best_seller'=>'Best Seller','recommended'=>'Recommended','vegetarian'=>'Vegetarian','spicy'=>'Spicy','halal'=>'Halal'] as $val => $lbl)
                            <label class="relative flex items-center cursor-pointer group">
                                <input type="checkbox" name="labels[]" value="{{ $val }}" :checked="formData.labels.includes('{{ $val }}')" class="peer sr-only">
                                <div class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 transition-all hover:border-amber-300">
                                    {{ $lbl }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black text-slate-800 uppercase tracking-widest">Status Ketersediaan</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Matikan jika menu sedang habis terjual</p>
                        </div>
                        <label class="relative inline-block cursor-pointer shrink-0">
                            <input type="checkbox" name="is_available" value="1" :checked="formData.is_available" class="sr-only peer" x-model="formData.is_available">
                            <div class="w-[52px] h-7 bg-slate-200 rounded-full peer-checked:bg-amber-500 transition-colors duration-200"></div>
                            <span class="absolute left-[3px] top-[3px] w-[22px] h-[22px] bg-white rounded-full shadow-sm transition-transform duration-200 peer-checked:translate-x-[25px]"></span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 pb-4">
                    <button type="button" @click="openModal = false" :disabled="submitting" class="flex-1 px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 bg-white border border-slate-100 rounded-2xl hover:bg-slate-50 transition-all">Batalkan</button>
                    <button type="submit" :disabled="submitting" class="flex-[2] px-8 py-4 bg-amber-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-amber-500/20 hover:bg-amber-600 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                        <span x-text="submitting ? 'Menyimpan...' : (editMode ? 'Perbarui Hidangan' : 'Terbitkan Menu')"></span>
                        <template x-if="submitting">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function menuManager() {
    return {
        openModal: false, 
        editMode: false,
        submitting: false,
        fileName: '',
        formData: { id:'', name:'', category_id:'', price:'', description:'', estimated_time:15, labels:[], is_available:true },
        errors: {},
        itemStatus: {
            @foreach($menuItems as $item)
                {{ $item->id }}: {{ $item->is_available ? 'true' : 'false' }},
            @endforeach
        },

        openCreateModal() {
            this.editMode = false;
            this.resetForm();
            this.openModal = true;
        },

        resetForm() {
            this.formData = { id:'', name:'', category_id:'', price:'', description:'', estimated_time:15, labels:[], is_available:true };
            this.errors = {};
            this.fileName = '';
            document.getElementById('menu-form').reset();
        },

        editItem(item) {
            this.editMode = true;
            this.errors = {};
            this.formData = { 
                id: item.id, 
                name: item.name, 
                category_id: item.category_id, 
                price: item.price, 
                description: item.description || '', 
                estimated_time: item.estimated_time || 15, 
                labels: item.labels || [], 
                is_available: item.is_available == 1 
            };
            this.fileName = 'Ubah Foto';
            this.openModal = true;
        },

        previewImage(e) {
            if (e.target.files.length > 0) {
                this.fileName = e.target.files[0].name;
            }
        },

        async handleSubmit(e) {
            this.submitting = true;
            this.errors = {};
            
            const form = e.target;
            const formData = new FormData(form);

            if (!this.formData.is_available) {
                formData.delete('is_available');
            }
            
            const url = this.editMode ? `/${@json(role_prefix())}/menu/${this.formData.id}` : '{{ role_route('admin.menu.store') }}';

            if (this.editMode) {
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    this.openModal = false;
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message || (this.editMode ? 'Menu berhasil diperbarui!' : 'Menu berhasil ditambahkan!'),
                        timer: 1800,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' }
                    });
                    window.location.reload();
                } else if (response.status === 422) {
                    this.errors = result.errors;
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Periksa kembali isian form Anda.',
                        customClass: { popup: 'rounded-2xl' }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || 'Terjadi kesalahan saat menyimpan.',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Terputus',
                    text: 'Periksa jaringan lalu coba lagi.',
                    customClass: { popup: 'rounded-2xl' }
                });
            } finally {
                this.submitting = false;
            }
        },

        async toggleStatus(id) {
            const isAvailable = this.itemStatus[id];
            const action = isAvailable ? 'Tandai Habis?' : 'Tandai Tersedia?';
            const text = isAvailable
                ? 'Menu ini akan ditandai habis dan tidak bisa dipesan pelanggan.'
                : 'Menu ini akan kembali tersedia untuk dipesan pelanggan.';

            const confirm = await Swal.fire({
                title: action,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isAvailable ? '#ef4444' : '#10b981',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            });

            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch(`/admin/menu/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    this.itemStatus[id] = result.is_available;
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.is_available ? 'Menu ditandai tersedia.' : 'Menu ditandai habis (sold out).',
                        timer: 1600,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' }
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memperbarui status menu.', customClass: { popup: 'rounded-2xl' } });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Koneksi Bermasalah', text: 'Coba lagi.', customClass: { popup: 'rounded-2xl' } });
            }
        },

        confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Hidangan?',
                text: "Menu ini akan hilang selamanya dari koleksi Anda.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const response = await fetch(`/admin/menu/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: (() => {
                            const fd = new FormData();
                            fd.append('_method', 'DELETE');
                            return fd;
                        })()
                    });
                    
                    const data = await response.json().catch(() => ({ success: response.ok }));
                    if (response.ok) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: data.message || 'Menu berhasil dihapus dari koleksi.',
                            timer: 1600,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-2xl' }
                        });
                        window.location.reload();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menghapus menu.', customClass: { popup: 'rounded-2xl' } });
                    }
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Koneksi Bermasalah', text: 'Coba lagi.', customClass: { popup: 'rounded-2xl' } });
                }
            });
        }
    }
}
</script>
@endpush
@endsection
