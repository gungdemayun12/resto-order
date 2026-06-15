<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pendapatan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }

        .header { margin-bottom: 18px; padding-bottom: 14px; border-bottom: 3px solid #f59e0b; }
        .header-inner { width: 100%; }
        .logo-cell { width: 70px; vertical-align: top; }
        .logo-cell img { width: 60px; height: 60px; object-fit: contain; }
        .title-cell { vertical-align: top; padding-left: 12px; }
        .brand-name { font-size: 22px; font-weight: 900; color: #1e293b; letter-spacing: -0.5px; }
        .doc-title { font-size: 13px; font-weight: 700; color: #f59e0b; margin-top: 2px; }
        .period-info { font-size: 9px; color: #64748b; margin-top: 3px; }

        .stats-grid { width: 100%; margin-bottom: 20px; }
        .stats-grid td { width: 25%; vertical-align: top; padding: 8px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; }
        .stat-label { font-size: 8px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .stat-value { font-size: 16px; font-weight: 900; color: #1e293b; margin-top: 2px; }
        .stat-value.revenue { color: #059669; }

        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 12px; font-weight: 900; color: #1e293b; border-left: 4px solid #f59e0b; padding-left: 8px; margin-bottom: 10px; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.data-table thead th {
            background: #1e293b; color: #ffffff; font-weight: 700; font-size: 8px;
            text-transform: uppercase; letter-spacing: 0.8px;
            padding: 8px 10px; text-align: left; border: none;
        }
        table.data-table thead th.right { text-align: right; }
        table.data-table thead th.center { text-align: center; }
        table.data-table tbody td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; }
        table.data-table tbody tr:nth-child(even) { background: #f8fafc; }
        table.data-table tbody td.right { text-align: right; font-variant-numeric: tabular-nums; }
        table.data-table tbody td.center { text-align: center; }
        .amount { font-weight: 700; color: #059669; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 700; }
        .badge-online { background: #dbeafe; color: #1d4ed8; }
        .badge-kasir { background: #f1f5f9; color: #475569; }

        .two-col { width: 100%; }
        .two-col td { width: 50%; vertical-align: top; padding-right: 10px; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 10px 20px;
                  border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-inner">
            <tr>
                <td class="logo-cell">
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Logo">
                    @endif
                </td>
                <td class="title-cell">
                    <div class="brand-name">RESTONUSA</div>
                    <div class="doc-title">LAPORAN PENDAPATAN</div>
                    <div class="period-info">Periode: {{ $periodLabel }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="stats-grid">
        <tr>
            <td>
                <div class="stat-card">
                    <div class="stat-label">Total Pesanan</div>
                    <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
                </div>
            </td>
            <td>
                <div class="stat-card">
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value revenue">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="stat-card">
                    <div class="stat-label">Rata-rata / Order</div>
                    <div class="stat-value">Rp {{ number_format($stats['avg_order_value'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="stat-card">
                    <div class="stat-label">Pesanan Selesai</div>
                    <div class="stat-value">{{ number_format($stats['completed_orders']) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Pendapatan Per Hari</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:35%">Tanggal</th>
                    <th class="right" style="width:35%">Pendapatan (Rp)</th>
                    <th class="center" style="width:30%">Jumlah Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenuePerDay as $d)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($d->date)->translatedFormat('d F Y') }}</td>
                    <td class="right amount">{{ number_format($d->revenue, 0, ',', '.') }}</td>
                    <td class="center">{{ $d->orders }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="two-col">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Menu Terlaris (Top 10)</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:10%">No</th>
                                <th style="width:45%">Menu</th>
                                <th class="center" style="width:20%">Qty</th>
                                <th class="right" style="width:25%">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topMenus as $i => $m)
                            <tr>
                                <td class="center">{{ $i + 1 }}</td>
                                <td>{{ $m->menuItem->name ?? '-' }}</td>
                                <td class="center">{{ $m->total_qty }}</td>
                                <td class="right amount">{{ number_format($m->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">Distribusi Status</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:60%">Status</th>
                                <th class="center" style="width:40%">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $statusLabels = ['pending'=>'Menunggu','processing'=>'Diproses','ready'=>'Siap','completed'=>'Selesai','cancelled'=>'Dibatalkan','payment_success'=>'Bayar Sukses','pending_payment'=>'Menunggu Bayar']; @endphp
                            @foreach($statusDistribution as $s)
                            <tr>
                                <td>{{ $statusLabels[$s->status] ?? $s->status }}</td>
                                <td class="center">{{ $s->count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Metode Pembayaran</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:30%">Metode</th>
                    <th class="center" style="width:20%">Jumlah Transaksi</th>
                    <th class="right" style="width:25%">Total Pendapatan (Rp)</th>
                    <th class="right" style="width:25%">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPayCount = $paymentMethods->sum('count'); @endphp
                @foreach($paymentMethods as $p)
                <tr>
                    <td>
                        <span class="badge {{ $p->payment_method === 'online' ? 'badge-online' : 'badge-kasir' }}">
                            {{ $p->payment_method === 'online' ? 'Online' : 'Kasir' }}
                        </span>
                    </td>
                    <td class="center">{{ number_format($p->count) }}</td>
                    <td class="right amount">{{ number_format($p->revenue, 0, ',', '.') }}</td>
                    <td class="right">{{ $totalPayCount > 0 ? number_format(($p->count / $totalPayCount) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="header" style="border-bottom-width:1px;">
        <table class="header-inner">
            <tr>
                <td class="logo-cell">
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Logo">
                    @endif
                </td>
                <td class="title-cell">
                    <div class="brand-name" style="font-size:16px;">RESTONUSA</div>
                    <div class="doc-title" style="font-size:11px;">RIWAYAT TRANSAKSI</div>
                    <div class="period-info">Periode: {{ $periodLabel }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:13%">No. Order</th>
                    <th class="center" style="width:7%">Meja</th>
                    <th style="width:18%">Pelanggan</th>
                    <th class="center" style="width:7%">Item</th>
                    <th class="right" style="width:18%">Total (Rp)</th>
                    <th class="center" style="width:12%">Pembayaran</th>
                    <th class="center" style="width:13%">Status</th>
                    <th style="width:12%">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td><strong>{{ $t->order_number }}</strong></td>
                    <td class="center">{{ $t->table->number ?? '-' }}</td>
                    <td>{{ $t->customer_name ?? '-' }}</td>
                    <td class="center">{{ $t->items->count() }}</td>
                    <td class="right amount">{{ number_format($t->total_amount, 0, ',', '.') }}</td>
                    <td class="center">
                        <span class="badge {{ $t->payment_method === 'online' ? 'badge-online' : 'badge-kasir' }}">
                            {{ $t->payment_method === 'online' ? 'Online' : 'Kasir' }}
                        </span>
                    </td>
                    <td class="center">{{ $t->status_label }}</td>
                    <td>{{ $t->created_at->translatedFormat('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak pada {{ $generatedAt }} &nbsp;|&nbsp; Laporan ini digenerate otomatis oleh sistem RESTONUSA
    </div>

</body>
</html>
