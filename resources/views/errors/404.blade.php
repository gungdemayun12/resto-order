<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Resto Nusantara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #1e293b;
        }

        .container {
            text-align: center;
            max-width: 440px;
            width: 100%;
        }

        .illustration {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 32px;
        }
        .plate {
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 50%;
            position: absolute;
            top: 10px;
            left: 10px;
            box-shadow: 0 8px 32px rgba(245, 158, 11, 0.15), inset 0 -4px 12px rgba(217, 119, 6, 0.1);
        }
        .plate::after {
            content: '';
            position: absolute;
            inset: 12px;
            border-radius: 50%;
            border: 2px dashed #fbbf24;
            opacity: 0.5;
        }
        .plate-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 64px;
            line-height: 1;
        }
        .question-mark {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: 900;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .error-code {
            font-size: 72px;
            font-weight: 900;
            color: #f59e0b;
            letter-spacing: -3px;
            line-height: 1;
            margin-bottom: 8px;
            text-shadow: 0 2px 0 #fde68a;
        }
        .error-title {
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .error-desc {
            font-size: 15px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 32px;
            font-weight: 400;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            width: 100%;
            max-width: 280px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.4);
        }
        .btn-secondary {
            background: white;
            color: #475569;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .btn-secondary:hover {
            border-color: #f59e0b;
            color: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .btn svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }
        .footer-brand {
            font-weight: 800;
            color: #f59e0b;
        }

        @media (min-width: 640px) {
            .btn-group { flex-direction: row; justify-content: center; }
            .btn { width: auto; }
            .error-code { font-size: 88px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <div class="plate">
                <div class="plate-icon">🍽️</div>
            </div>
            <div class="question-mark">?</div>
        </div>

        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">
            Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.
            Mungkin Anda salah alamat atau menu ini sudah tidak tersedia.
        </p>

        <div class="btn-group">
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <a href="/" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Ke Halaman Utama
            </a>
        </div>

        <div class="footer">
            <p class="footer-text">
                <span class="footer-brand">Resto Nusantara</span> &middot; Sistem Pemesanan Digital
            </p>
        </div>
    </div>
</body>
</html>
