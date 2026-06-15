@extends('layouts.customer')
@section('title', 'Informasi Pelanggan' . ($table ? ' - Meja ' . $table->number : ''))

@section('content')
    <div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 border border-slate-100">
            
            <div class="text-center mb-8">
                <div @click="bypassLocation()"
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-amber-400 to-amber-600 rounded-3xl mb-5 shadow-[0_8px_30px_rgba(245,158,11,0.3)] text-white transform rotate-3 hover:rotate-6 transition-transform cursor-pointer">
                    <svg class="w-10 h-10 -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h1 class="font-display text-3xl font-bold text-slate-800 tracking-tight">Selamat Datang</h1>
                <p class="text-sm text-slate-500 mt-2">Silakan lengkapi data diri Anda untuk mulai memesan</p>
            </div>

            @if(session('warning'))
                <div class="mb-6 p-4 bg-yellow-50 text-yellow-700 rounded-xl text-sm border border-yellow-200">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl text-sm border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            
            <form method="POST" action="{{ route('menu.storeSession') }}" class="space-y-5" x-data="{ 
                      name: '', 
                      phone: '', 
                      tableNumber: '{{ $table ? $table->number : '' }}',

                      // Geolocation Security
                      isLocating: true,
                      locationError: '',
                      locationValid: false,
                      bypassCount: 0,

                      // SETUP KOORDINAT RESTORAN DI SINI (Contoh: Monas)
                      restoLat: -6.175392, 
                      restoLng: 106.827153,
                      maxDistance: 500, // Maksimal jarak dalam meter

                      verifyLocation() {
                          this.isLocating = true;
                          this.locationError = '';

                          if (!navigator.geolocation) {
                              this.locationError = 'Browser Anda tidak mendukung deteksi lokasi. Tidak bisa memverifikasi.';
                              this.isLocating = false;
                              return;
                          }

                          navigator.geolocation.getCurrentPosition(
                              (position) => {
                                  const lat = position.coords.latitude;
                                  const lng = position.coords.longitude;

                                  // Haversine formula
                                  const R = 6371e3; // metres
                                  const p1 = this.restoLat * Math.PI/180;
                                  const p2 = lat * Math.PI/180;
                                  const dp = (lat - this.restoLat) * Math.PI/180;
                                  const dl = (lng - this.restoLng) * Math.PI/180;

                                  const a = Math.sin(dp/2) * Math.sin(dp/2) +
                                            Math.cos(p1) * Math.cos(p2) *
                                            Math.sin(dl/2) * Math.sin(dl/2);
                                  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                                  const d = R * c;

                                  if (d > this.maxDistance) {
                                      this.locationError = 'Sistem mendeteksi Anda berada di luar area restoran (' + Math.round(d/1000) + ' km). Untuk keamanan, pemesanan hanya bisa dilakukan di lokasi.';
                                      this.locationValid = false;
                                  } else {
                                      this.locationValid = true;
                                  }
                                  this.isLocating = false;
                              },
                              (error) => {
                                  if(error.code === 1) {
                                      this.locationError = 'Akses lokasi ditolak. Kami membutuhkan akses lokasi untuk mencegah fake order dari luar restoran.';
                                  } else {
                                      this.locationError = 'Gagal mendeteksi lokasi Anda. Pastikan GPS menyala.';
                                  }
                                  this.isLocating = false;
                              },
                              { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                          );
                      },

                      bypassLocation() {
                          this.bypassCount++;
                          if(this.bypassCount >= 5) {
                              this.locationValid = true;
                              this.locationError = '';
                              this.isLocating = false;
                              alert('Developer mode: Security bypassed.');
                          }
                      }
                  }" x-init="verifyLocation()">
                @csrf

                
                <div x-show="locationError"
                    class="p-4 bg-red-50 text-red-700 rounded-xl text-sm border border-red-200 flex items-start gap-3"
                    style="display: none;">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1">
                        <strong class="block mb-1">Keamanan Lokasi</strong>
                        <span x-text="locationError"></span>
                        <button type="button" @click="verifyLocation()"
                            class="mt-2 text-red-600 font-bold hover:underline block">Coba Deteksi Ulang</button>
                    </div>
                </div>

                <div x-show="isLocating"
                    class="p-4 bg-blue-50 text-blue-700 rounded-xl text-sm border border-blue-200 flex items-center gap-3">
                    <svg class="animate-spin w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Mendeteksi lokasi Anda untuk verifikasi keamanan...</span>
                </div>

                <div x-show="locationValid && !isLocating"
                    class="p-4 bg-emerald-50 text-emerald-700 rounded-xl text-sm border border-emerald-200 flex items-center gap-3"
                    style="display: none;">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Lokasi terverifikasi. Anda berada di area restoran.</span>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Meja <span
                            class="text-red-500">*</span></label>
                    @if($table)
                        <div class="relative">
                            <input type="text" value="{{ $table->number }} ({{ $table->location_label }})" readonly
                                class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-600 font-semibold cursor-not-allowed outline-none">
                            <input type="hidden" name="table_number" value="{{ $table->number }}">
                        </div>
                    @else
                        <input type="number" name="table_number" x-model="tableNumber" required min="1"
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                            placeholder="Masukkan nomor meja Anda">
                    @endif
                    @error('table_number')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" x-model="name" required
                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                        placeholder="Masukkan nama Anda">
                    @error('customer_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Telepon <span
                            class="text-red-500">*</span></label>
                    <input type="tel" name="customer_phone" x-model="phone" required pattern="[0-9]{10,13}"
                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                        placeholder="Contoh: 081234567890">
                    <p class="mt-1 text-xs text-slate-400">Gunakan format angka, 10-13 digit.</p>
                    @error('customer_phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                
                <button type="submit"
                    :disabled="isLocating || !locationValid || name.trim() === '' || phone.replace(/\D/g,'').length < 10 || phone.replace(/\D/g,'').length > 13 || String(tableNumber).trim() === ''"
                    class="group w-full py-4 md:py-5 mt-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-black text-base md:text-lg uppercase tracking-widest rounded-2xl hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition-all shadow-xl shadow-amber-600/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 transform hover:scale-[1.02] active:scale-[0.98]">
                    <span x-text="isLocating ? 'Memverifikasi...' : 'Lanjutkan'"></span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
@endsection