<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Selamat Datang - Meja #{{ $tableNumber }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }

        .input-focus {
            @apply ring-4 ring-amber-500/10 border-amber-500 bg-white;
        }

        .error-message {
            @apply text-red-500 text-xs mt-1 ml-1;
        }

        .input-error {
            @apply border-red-300 bg-red-50 focus:ring-red-500 focus:border-red-500;
        }
    </style>
</head>

<body class="bg-pattern min-h-screen">

    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-amber-400/20 blur-[120px] rounded-full -z-10"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-400/10 blur-[120px] rounded-full -z-10"></div>

    <div class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="max-w-md w-full">

            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center mb-6">
                    <x-resto-logo size="lg" class="drop-shadow-lg" />
                </div>
                <p class="text-slate-500 font-medium italic">Satu langkah lagi menuju hidangan lezat</p>
                <div
                    class="mt-4 inline-flex items-center gap-2 px-4 py-1.5 bg-amber-100 text-amber-800 rounded-full text-xs font-bold">
                    <span class="w-2 h-2 bg-amber-500 rounded-full animate-ping"></span>
                    Meja #{{ $tableNumber }} - Sesi Aktif
                </div>
            </div>

            <div class="glass-card rounded-[2.5rem] shadow-2xl shadow-slate-200/50 p-8 md:p-10 border border-white">
                <form id="customer-form" class="space-y-6" novalidate>
                    <input type="hidden" name="session_token" value="{{ $session->session_token }}">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div class="form-group">
                        <label for="customer_name"
                            class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Siapa Nama Anda?
                        </label>
                        <div class="relative">
                            <input type="text" id="customer_name" name="customer_name"
                                placeholder="Masukkan nama lengkap"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:bg-white transition-all outline-none text-slate-800 placeholder-slate-400 font-semibold shadow-sm">
                        </div>
                        <p id="error-customer_name" class="text-red-500 text-xs mt-1 ml-1 hidden"></p>
                    </div>

                    <div class="form-group">
                        <label for="customer_phone"
                            class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Nomor WhatsApp
                        </label>
                        <div class="relative group">
                            <div
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold pointer-events-none">
                                +62
                            </div>

                            <input
                                type="tel"
                                id="customer_phone"
                                name="customer_phone"
                                placeholder="8123xxxxxxx"
                                class="w-full pl-14 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:bg-white transition-all outline-none text-slate-800 placeholder-slate-400 font-semibold shadow-sm">
                        </div>
                        <p id="error-customer_phone" class="text-red-500 text-xs mt-1 ml-1 hidden"></p>
                        <p class="text-[10px] text-slate-400 mt-2.5 ml-1 font-medium leading-relaxed">Kami akan
                            mengirimkan rincian pesanan dan status hidangan melalui nomor ini.</p>
                    </div>

                    <div id="location-status"
                        class="hidden flex items-center gap-2 p-4 bg-emerald-50 text-emerald-700 rounded-2xl text-[11px] font-bold border border-emerald-100 shadow-sm animate-in fade-in slide-in-from-top-2">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        Lokasi Terverifikasi (Geo-Fencing Aktif)
                    </div>

                    <button type="submit" id="submit-btn"
                        class="group w-full bg-gradient-to-r from-amber-500 to-amber-600 text-white py-4 md:py-5 px-6 rounded-2xl font-black text-base md:text-lg uppercase tracking-widest hover:from-amber-600 hover:to-amber-700 transition-all shadow-xl shadow-amber-600/30 flex items-center justify-center gap-3 active:scale-[0.98] transform hover:scale-[1.02]">
                        <span id="btn-text">Lanjutkan</span>
                        <svg id="btn-icon" class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <div id="btn-loading" class="hidden">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </button>
                </form>
            </div>

            <div class="mt-10 flex flex-col items-center justify-center gap-4">
                <div class="flex flex-wrap items-center gap-6 opacity-70 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                        class="h-6 w-auto object-contain"
                        alt="Mastercard"
                        loading="lazy"
                    >

                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg"
                        class="h-6 w-auto object-contain"
                        alt="BCA"
                        loading="lazy"
                    >

                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg"
                        class="h-6 w-auto object-contain"
                        alt="Mandiri"
                        loading="lazy"
                    >

                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg"
                        class="h-6 w-auto object-contain"
                        alt="GoPay"
                        loading="lazy"
                    >

                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/f/fe/Shopee.svg"
                        class="h-6 w-auto object-contain"
                        alt="ShopeePay"
                        loading="lazy"
                    >
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] flex items-center gap-2">
                    <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Protected by End-to-End Encryption
                </p>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        document.getElementById('location-status').classList.remove('hidden');
                    },
                    (error) => {
                        console.warn('Location access denied or unavailable');
                    }
                );
            }
        });

        document.getElementById('customer-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            document.querySelectorAll('.error-message').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
            document.querySelectorAll('input').forEach(el => el.classList.remove('input-error'));

            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnIcon = document.getElementById('btn-icon');
            const btnLoading = document.getElementById('btn-loading');

            submitBtn.disabled = true;
            btnText.textContent = 'Memproses...';
            btnIcon.classList.add('hidden');
            btnLoading.classList.remove('hidden');

            const formData = new FormData(this);
            const rawPhone = formData.get('customer_phone');
            const data = {
                session_token: formData.get('session_token'),
                customer_name: formData.get('customer_name'),
                customer_phone: rawPhone ? '62' + rawPhone : '',
                latitude: formData.get('latitude'),
                longitude: formData.get('longitude')
            };

            try {
                const response = await fetch('{{ route("customer.form.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.status === 200 && result.success) {
                    btnText.textContent = 'Menuju Menu...';
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 500);
                } else if (response.status === 422) {

                    const errors = result.errors;
                    for (const key in errors) {
                        const errorEl = document.getElementById('error-' + key);
                        const inputEl = document.getElementById(key);
                        if (errorEl) {
                            errorEl.textContent = errors[key][0];
                            errorEl.classList.remove('hidden');
                        }
                        if (inputEl) {
                            inputEl.classList.add('input-error');
                        }
                    }
                    resetButton();
                } else {
                    alert(result.message || 'Terjadi kesalahan sistem.');
                    resetButton();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Koneksi terputus. Silakan coba lagi.');
                resetButton();
            }

            function resetButton() {
                submitBtn.disabled = false;
                btnText.textContent = 'Lanjutkan';
                btnIcon.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        });

        document.getElementById('customer_phone').addEventListener('input', function (e) {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val.startsWith('0')) {
                val = val.substring(1);
            } else if (val.startsWith('62')) {
                val = val.substring(2);
            }
            this.value = val;
        });

        document.querySelectorAll('input[type="text"], input[type="tel"]').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('scale-[1.02]');
                input.parentElement.classList.add('transition-transform');
            });
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('scale-[1.02]');
            });
        });
    </script>
</body>

</html>