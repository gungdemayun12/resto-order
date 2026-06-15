<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->order_number }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #d1d5db;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 20px 12px 40px;
            font-family: 'Courier New', 'Courier', 'Liberation Mono', monospace;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .receipt-wrap {
            width: 100%;
            max-width: 320px; /* ~80mm thermal */
        }

        .tear-top {
            width: 100%;
            height: 12px;
            background: linear-gradient(135deg, #fff 33.33%, transparent 33.33%) 0 0,
                        linear-gradient(225deg, #fff 33.33%, transparent 33.33%) 0 0;
            background-size: 8px 12px;
            background-repeat: repeat-x;
        }

        .receipt {
            background: #fff;
            padding: 20px 18px 24px;
            color: #000;
            font-size: 12px;
            line-height: 1.5;
        }

        .tear-bottom {
            width: 100%;
            height: 12px;
            background: linear-gradient(315deg, #fff 33.33%, transparent 33.33%) 0 0,
                        linear-gradient(45deg, #fff 33.33%, transparent 33.33%) 0 0;
            background-size: 8px 12px;
            background-repeat: repeat-x;
        }

        .r-center { text-align: center; }
        .r-bold { font-weight: 700; }
        .r-line { border-top: 1px dashed #000; margin: 10px 0; }
        .r-dline { border-top: 2px dashed #000; margin: 12px 0; }
        .r-dotline { border-top: 1px dotted #000; margin: 8px 0; }
        .r-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
        .r-right { text-align: right; }
        .r-nowrap { white-space: nowrap; }

        .r-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            margin: 0 auto 8px;
            display: block;
        }

        .r-name {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .r-tagline {
            font-size: 9px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
            opacity: 0.6;
        }

        .r-section-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .r-item { margin-bottom: 10px; }
        .r-item-name {
            font-weight: 700;
            font-size: 12px;
            word-break: break-word;
        }
        .r-item-detail {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 1px;
        }
        .r-item-note {
            font-size: 10px;
            font-style: italic;
            opacity: 0.6;
            margin-top: 2px;
            padding-left: 8px;
        }

        .r-total-label {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .r-total-amount {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .r-qr-wrap {
            display: flex;
            justify-content: center;
            margin: 12px 0 8px;
        }
        .r-qr {
            padding: 6px;
            border: 1px solid #000;
        }
        .r-qr svg, .r-qr img {
            display: block;
        }

        .r-footer-text {
            font-size: 10px;
            opacity: 0.5;
            letter-spacing: 0.5px;
        }

        .r-barcode {
            display: flex;
            justify-content: center;
            gap: 1px;
            margin-top: 10px;
            opacity: 0.2;
        }
        .r-barcode span {
            display: block;
            width: 2px;
            background: #000;
        }

        .r-paid-stamp {
            display: inline-block;
            border: 2px solid #000;
            padding: 3px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 8px;
            transform: rotate(-4deg);
        }

        .actions {
            width: 100%;
            max-width: 320px;
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 14px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn:active { transform: scale(0.97); }
        .btn-dark { background: #111; color: #fff; }
        .btn-dark:hover { background: #333; }
        .btn-amber { background: #f59e0b; color: #fff; box-shadow: 0 4px 12px rgba(245,158,11,0.3); }
        .btn-amber:hover { background: #d97706; }
        .btn-back { background: #fff; color: #333; border: 1.5px solid #ddd; }
        .btn-back:hover { border-color: #999; }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                background: #fff;
                padding: 0;
                display: block;
            }
            .receipt-wrap { max-width: 80mm; }
            .tear-top, .tear-bottom { display: none; }
            .receipt { padding: 4mm 3mm 6mm; font-size: 11px; }
            .actions { display: none !important; }
            .r-logo { width: 40px; height: 40px; }
            .r-name { font-size: 14px; }
            .r-total-amount { font-size: 16px; }
            .r-qr-wrap { margin: 8px 0 4px; }
        }
    </style>
</head>
<body>

    <div class="receipt-wrap" id="receipt-capture">
        <div class="tear-top"></div>

        <div class="receipt">

            <div class="r-center" style="padding-bottom:12px;">
                @if(file_exists(public_path('storage/logo.png')))
                    <img src="{{ asset('storage/logo.png') }}" class="r-logo" alt="Logo">
                @endif
                <div class="r-name">Resto Nusantara</div>
                <div class="r-tagline">Sistem Pemesanan Digital</div>
            </div>

            <div class="r-line"></div>

            <div class="r-center" style="padding:8px 0;">
                <div class="r-paid-stamp">
                    {{ $order->payment_status === 'paid' ? '★ LUNAS ★' : 'BELUM LUNAS' }}
                </div>
            </div>

            <div class="r-line"></div>

            <div style="padding:6px 0;">
                <div class="r-row" style="margin-bottom:4px;">
                    <span>No. Order</span>
                    <span class="r-bold">#{{ $order->order_number }}</span>
                </div>
                <div class="r-row" style="margin-bottom:4px;">
                    <span>Tanggal</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="r-row" style="margin-bottom:4px;">
                    <span>Meja</span>
                    <span class="r-bold">{{ $order->table->number }}</span>
                </div>
                <div class="r-row">
                    <span>Pembayaran</span>
                    <span>{{ $order->payment_method === 'cashier' ? 'Cash / Kasir' : 'Online' }}</span>
                </div>
            </div>

            <div class="r-dline"></div>

            <div class="r-section-title">Detail Pesanan</div>
            <div>
                @foreach($order->items as $item)
                    <div class="r-item">
                        <div class="r-row">
                            <div style="flex:1;min-width:0;">
                                <div class="r-item-name">{{ $item->menuItem->name }}</div>
                                <div class="r-item-detail">
                                    {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                </div>
                                @if($item->notes)
                                    <div class="r-item-note">↳ {{ $item->notes }}</div>
                                @endif
                            </div>
                            <span class="r-bold r-nowrap" style="font-size:12px;">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="r-dline"></div>

            <div style="padding:2px 0;">
                <div class="r-row" style="margin-bottom:4px;">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="r-row" style="margin-bottom:8px;">
                    <span>PPN (11%)</span>
                    <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                </div>
                <div class="r-dotline"></div>
                <div class="r-row" style="padding-top:4px;">
                    <span class="r-total-label">TOTAL</span>
                    <span class="r-total-amount">{{ $order->formatted_total }}</span>
                </div>
            </div>

            <div class="r-line"></div>

            <div class="r-center" style="padding-top:8px;">
                <div style="font-size:9px;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;opacity:0.5;">
                    Scan untuk Lacak Pesanan
                </div>
                <div class="r-qr-wrap">
                    <div class="r-qr">
                        {!! QrCode::size(100)->margin(0)->generate(route('order.tracking', $order->id)) !!}
                    </div>
                </div>

                <div class="r-line"></div>

                <div class="r-footer-text" style="margin-top:8px;">
                    Terima kasih atas kunjungan Anda!
                </div>
                <div class="r-footer-text" style="margin-top:2px;">
                    Selamat menikmati hidangan Anda
                </div>

                <div class="r-barcode">
                    @php $seed = crc32($order->order_number); @endphp
                    @for($i = 0; $i < 40; $i++)
                        @php $h = 16 + (($seed * ($i + 1)) % 12); @endphp
                        <span style="height:{{ $h }}px;{{ $i % 3 === 0 ? 'width:1px;' : '' }}"></span>
                    @endfor
                </div>
            </div>

        </div>

        <div class="tear-bottom"></div>
    </div>

    <div class="actions" id="action-bar">
        <button class="btn btn-dark" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
            </svg>
            Cetak Struk
        </button>

        <button class="btn btn-amber" id="btn-png" onclick="downloadPNG()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Simpan PNG
        </button>

        <a href="javascript:history.back()" class="btn btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <script>
        async function downloadPNG() {
            const btn = document.getElementById('btn-png');
            const orig = btn.innerHTML;
            btn.innerHTML = '<span>Membuat gambar...</span>';
            btn.style.opacity = '0.6';
            btn.style.pointerEvents = 'none';

            document.getElementById('action-bar').style.display = 'none';

            try {
                const target = document.getElementById('receipt-capture');
                const canvas = await html2canvas(target, {
                    scale: 3,
                    useCORS: true,
                    backgroundColor: '#d1d5db',
                    logging: false,
                    windowWidth: target.scrollWidth,
                    windowHeight: target.scrollHeight,
                });
                const link = document.createElement('a');
                link.download = 'struk-{{ $order->order_number }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            } catch (e) {
                alert('Gagal membuat gambar. Coba lagi.');
            } finally {
                document.getElementById('action-bar').style.display = '';
                btn.innerHTML = orig;
                btn.style.opacity = '';
                btn.style.pointerEvents = '';
            }
        }
    </script>
</body>
</html>
