@extends('layouts.admin')

@section('title', 'Scan Barcode Order')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Scan Barcode Order</h1>
            <p class="text-gray-600">Scan barcode dari customer untuk konfirmasi pembayaran</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Masukkan Nomor Order atau Scan Barcode
                </label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        id="orderNumberInput" 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-mono"
                        placeholder="ORD-20260511-0001"
                        autofocus
                    >
                    <button 
                        onclick="scanOrder()" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold"
                    >
                        Scan
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-2">Tekan Enter atau klik tombol Scan</p>
            </div>

            <canvas id="qrCanvas" class="hidden"></canvas>

            <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

            <div class="text-center py-4 border-t mt-4">
                <button 
                    onclick="toggleCamera()" 
                    class="text-blue-600 hover:text-blue-700 font-medium"
                >
                    📷 Gunakan Kamera untuk Scan
                </button>
                <div id="cameraSection" class="hidden mt-4">
                    <video id="video" class="w-full max-w-md mx-auto rounded-lg border" autoplay></video>
                    <canvas id="canvas" class="hidden"></canvas>
                </div>
            </div>
        </div>

        <div id="resultSection" class="hidden">
            <div id="successResult" class="bg-green-50 border border-green-200 rounded-lg p-6 mb-4 hidden">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Order Berhasil Dikonfirmasi!</h3>
                        <div id="orderDetails" class="text-sm text-green-700"></div>
                        <button onclick="resetScanner()" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Scan Order Berikutnya
                        </button>
                    </div>
                </div>
            </div>

            <div id="errorResult" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-4 hidden">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-semibold text-red-800 mb-2">Gagal Konfirmasi Order</h3>
                        <p id="errorMessage" class="text-sm text-red-700"></p>
                        <button onclick="resetScanner()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Scan Terakhir</h3>
            <div id="recentScans" class="space-y-2">
                <p class="text-gray-500 text-sm">Belum ada scan</p>
            </div>
        </div>

    <script>
    const orderInput = document.getElementById('orderNumberInput');
    const resultSection = document.getElementById('resultSection');
    const successResult = document.getElementById('successResult');
    const errorResult = document.getElementById('errorResult');
    const orderDetails = document.getElementById('orderDetails');
    const errorMessage = document.getElementById('errorMessage');
    const recentScans = document.getElementById('recentScans');
    const qrCanvas = document.getElementById('qrCanvas');
    const scanImageBtn = document.getElementById('scanImageBtn');

    let recentScansList = [];

    // Enter key handler
    orderInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            scanOrder();
        }
    });

    async function scanOrder() {
        const orderNumber = orderInput.value.trim();
        
        if (!orderNumber) {
            alert('Masukkan nomor order terlebih dahulu');
            return;
        }

        // Show loading
        orderInput.disabled = true;
        
        try {
            const response = await fetch('{{ role_route('admin.orders.scan-barcode') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_number: orderNumber })
            });

            const data = await response.json();

            resultSection.classList.remove('hidden');

            if (data.success) {
                // Show success
                successResult.classList.remove('hidden');
                errorResult.classList.add('hidden');
                
                orderDetails.innerHTML = `
                    <p><strong>Order:</strong> ${data.order.order_number}</p>
                    <p><strong>Customer:</strong> ${data.order.customer_name}</p>
                    <p><strong>Meja:</strong> ${data.order.table.table_number}</p>
                    <p><strong>Total:</strong> Rp ${new Intl.NumberFormat('id-ID').format(data.order.total_amount)}</p>
                    <p><strong>Status:</strong> <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">${data.order.status}</span></p>
                `;

                // Add to recent scans
                addToRecentScans(data.order);

                // Play success sound (optional)
                playSound('success');
            } else {
                // Show error
                successResult.classList.add('hidden');
                errorResult.classList.remove('hidden');
                errorMessage.textContent = data.message;

                // Play error sound (optional)
                playSound('error');
            }
        } catch (error) {
            console.error('Error:', error);
            resultSection.classList.remove('hidden');
            successResult.classList.add('hidden');
            errorResult.classList.remove('hidden');
            errorMessage.textContent = 'Terjadi kesalahan saat memproses scan';
        } finally {
            orderInput.disabled = false;
        }
    }

    function resetScanner() {
        orderInput.value = '';
        orderInput.focus();
        resultSection.classList.add('hidden');
        successResult.classList.add('hidden');
        errorResult.classList.add('hidden');
    }

    function addToRecentScans(order) {
        const scanTime = new Date().toLocaleTimeString('id-ID');
        recentScansList.unshift({
            order_number: order.order_number,
            customer_name: order.customer_name,
            time: scanTime
        });

        // Keep only last 5 scans
        if (recentScansList.length > 5) {
            recentScansList.pop();
        }

        updateRecentScans();
    }

    function updateRecentScans() {
        if (recentScansList.length === 0) {
            recentScans.innerHTML = '<p class="text-gray-500 text-sm">Belum ada scan</p>';
            return;
        }

        recentScans.innerHTML = recentScansList.map(scan => `
            <div class="flex justify-between items-center py-2 border-b">
                <div>
                    <p class="font-medium text-gray-800">${scan.order_number}</p>
                    <p class="text-sm text-gray-600">${scan.customer_name}</p>
                </div>
                <span class="text-sm text-gray-500">${scan.time}</span>
            </div>
        `).join('');
    }

    function playSound(type) {
        // Optional: Add sound effects
        // const audio = new Audio(type === 'success' ? '/sounds/success.mp3' : '/sounds/error.mp3');
        // audio.play();
    }

    function toggleCamera() {
        const cameraSection = document.getElementById('cameraSection');
        cameraSection.classList.toggle('hidden');
        
        if (!cameraSection.classList.contains('hidden')) {
            startCamera();
        } else {
            stopCamera();
        }
    }

    let stream = null;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            const video = document.getElementById('video');
            video.srcObject = stream;
            
            // Start scanning frames from camera
            video.addEventListener('play', function() {
                requestAnimationFrame(scanCameraFrame);
            });
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera');
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    function scanCameraFrame() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        
        if (!video.videoWidth || !video.videoHeight) {
            if (stream) {
                requestAnimationFrame(scanCameraFrame);
            }
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth',
        });

        if (code) {
            orderInput.value = code.data;
            stopCamera();
            document.getElementById('cameraSection').classList.add('hidden');
            scanOrder();
            return;
        }

        if (stream) {
            requestAnimationFrame(scanCameraFrame);
        }
    }

    // Auto focus on input when page loads
    window.addEventListener('load', function() {
        orderInput.focus();
        loadPendingOrders();
    });

    // Load pending confirmation orders for quick test
    async function loadPendingOrders() {
        try {
            const response = await fetch('/admin/orders/get-latest');
            const data = await response.json();
            
            // Filter orders with pending_confirmation status
            const pendingOrders = data.orders.filter(order => order.status === 'pending_confirmation');
            
            if (pendingOrders.length > 0) {
                const quickTestButtons = document.getElementById('quickTestButtons');
                quickTestButtons.innerHTML = pendingOrders.slice(0, 5).map(order => `
                    <button 
                        onclick="quickTest('${order.order_number}')"
                        class="px-3 py-2 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition-colors font-mono"
                    >
                        ${order.order_number}
                    </button>
                `).join('');
            } else {
                const quickTestButtons = document.getElementById('quickTestButtons');
                quickTestButtons.innerHTML = '<p class="text-xs text-purple-600">Tidak ada order yang menunggu konfirmasi</p>';
            }
        } catch (error) {
            console.error('Error loading pending orders:', error);
        }
    }

    // Quick test function
    async function quickTest(orderNumber) {
        orderInput.value = orderNumber;
        await scanOrder();
    }

    // Preview image when selected
    document.getElementById('qrImageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                previewImg.src = event.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    /**
     * Scan QR Code from uploaded image using jsqr
     * jsqr is a pure JS QR decoder that works with ImageData from canvas.
     * This approach is lightweight and reliable for static images.
     */
    async function scanFromImage() {
        const fileInput = document.getElementById('qrImageInput');
        const file = fileInput.files[0];
        
        if (!file) {
            alert('Pilih gambar terlebih dahulu');
            return;
        }

        // Show loading
        const originalText = scanImageBtn.textContent;
        scanImageBtn.disabled = true;
        scanImageBtn.textContent = 'Memproses...';

        try {
            // Load the image
            const img = await loadImage(file);

            // Try multiple scale factors to handle different QR code sizes
            const scaleFactors = [1, 2, 0.5, 3, 0.75, 1.5, 4, 0.25];
            let decodedText = null;

            const canvas = document.getElementById('qrCanvas');
            const ctx = canvas.getContext('2d', { willReadFrequently: true });

            for (const scale of scaleFactors) {
                const newWidth = Math.floor(img.width * scale);
                const newHeight = Math.floor(img.height * scale);

                // Skip if dimensions become too large
                if (newWidth > 4096 || newHeight > 4096) continue;

                // Scale image on canvas
                canvas.width = newWidth;
                canvas.height = newHeight;
                ctx.clearRect(0, 0, newWidth, newHeight);
                ctx.drawImage(img, 0, 0, newWidth, newHeight);

                const imageData = ctx.getImageData(0, 0, newWidth, newHeight);

                // Try jsqr with different inversion attempts
                for (const inversionAttempt of ['dontInvert', 'invert', 'attemptBoth']) {
                    try {
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: inversionAttempt,
                        });

                        if (code) {
                            decodedText = code.data;
                            console.log('QR Code decoded:', decodedText);
                            break;
                        }
                    } catch (e) {
                        // Skip and try next
                    }
                }

                if (decodedText) break;
            }

            if (decodedText) {
                // Validate format: should be ORD-YYYYMMDD-XXXX
                const orderPattern = /ORD-\d{8}-\d{4}/i;
                if (orderPattern.test(decodedText)) {
                    orderInput.value = decodedText.toUpperCase();
                    await scanOrder();
                } else {
                    // QR scanned but doesn't look like an order number
                    alert(`QR code berhasil dibaca, tetapi format tidak sesuai.\n\nIsi QR: ${decodedText}\n\nHarap pastikan ini adalah QR code order.`);
                    orderInput.value = decodedText;
                    orderInput.focus();
                }
            } else {
                // All methods failed - throw to catch block
                throw new Error('jsqr could not decode QR code from image');
            }

        } catch (error) {
            console.error('Image scan failed:', error);
            alert('❌ Gagal membaca QR code dari gambar.\n\n✅ Solusi:\n1. Lihat nomor order di gambar (format: ORD-YYYYMMDD-XXXX)\n2. Ketik manual di input "Masukkan Nomor Order" di atas\n3. Tekan Enter atau klik Scan\n\nContoh: ORD-20260511-0017');
        } finally {
            scanImageBtn.disabled = false;
            scanImageBtn.textContent = originalText;
        }
    }

    /**
     * Helper: Load an image file into an HTML Image element
     * Returns a Promise that resolves with the loaded Image.
     */
    function loadImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    resolve(img);
                };
                img.onerror = function() {
                    reject(new Error('Failed to load image'));
                };
                img.src = event.target.result;
            };
            reader.onerror = function() {
                reject(new Error('Failed to read file'));
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection