@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('subtitle', 'Analisis pendapatan, menu terpopuler, dan tren penjualan')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="GET" id="report-form" class="space-y-4">

            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest mr-1">Periode:</span>

                @foreach(['today' => 'Hari Ini', 'week' => '7 Hari', 'month' => '30 Hari', 'quarter' => '3 Bulan'] as $k => $v)
                <button type="submit" name="range" value="{{ $k }}"
                    class="px-4 py-2 rounded-xl text-sm font-bold transition-all
                    {{ ($range ?? 'week') === $k ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
                    {{ $v }}
                </button>
                @endforeach

                <span class="text-slate-300 font-bold mx-1">|</span>

                <input type="month" name="month" id="month-picker"
                    value="{{ $selectedMonth ?? '' }}"
                    title="Pilih bulan"
                    class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500
                    {{ ($range ?? '') === 'month_picker' ? 'ring-2 ring-amber-500 bg-amber-50' : '' }}"
                    onchange="document.getElementById('range-input').value='month_picker'; document.getElementById('report-form').submit();">

                <select name="year" title="Pilih tahun"
                    class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500
                    {{ ($range ?? '') === 'year' ? 'ring-2 ring-amber-500 bg-amber-50' : '' }}"
                    onchange="document.getElementById('range-input').value='year'; document.getElementById('report-form').submit();">
                    @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>

                <input type="hidden" name="range" id="range-input" value="{{ $range ?? 'week' }}">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest mr-1">Kustom:</span>
                <input type="text" name="start_date" id="start-date"
                    value="{{ ($range ?? '') === 'custom' && isset($startDate) ? $startDate->format('Y-m-d') : '' }}"
                    placeholder="Tanggal Mulai"
                    class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm w-44 focus:ring-2 focus:ring-amber-500">
                <span class="text-slate-300 font-bold">→</span>
                <input type="text" name="end_date" id="end-date"
                    value="{{ ($range ?? '') === 'custom' && isset($endDate) ? $endDate->format('Y-m-d') : '' }}"
                    placeholder="Tanggal Akhir"
                    class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm w-44 focus:ring-2 focus:ring-amber-500">
                <button type="submit"
                    onclick="document.getElementById('range-input').value='custom'"
                    class="px-5 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-all">
                    Terapkan
                </button>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Menampilkan data:
                    <span class="font-semibold text-slate-700">
                        {{ $startDate->translatedFormat('d F Y') }} – {{ $endDate->translatedFormat('d F Y') }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Export:</span>
                    <button type="button" onclick="exportReport('pdf')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-bold
                               hover:bg-red-700 active:scale-95 transition-all shadow-sm shadow-red-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        PDF
                    </button>
                    <button type="button" onclick="exportReport('excel')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold
                               hover:bg-emerald-700 active:scale-95 transition-all shadow-sm shadow-emerald-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-lg hover:shadow-slate-100/50 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Total</span>
            </div>
            <p class="text-3xl font-black text-slate-800 group-hover:text-amber-600 transition-colors">{{ number_format($stats['total_orders']) }}</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Total Pesanan</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-lg hover:shadow-slate-100/50 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[9px] font-black text-emerald-300 uppercase tracking-widest">Revenue</span>
            </div>
            <p class="text-2xl font-black text-emerald-600 group-hover:text-emerald-700 transition-colors">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Total Pendapatan</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-lg hover:shadow-slate-100/50 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="text-[9px] font-black text-blue-300 uppercase tracking-widest">Avg</span>
            </div>
            <p class="text-2xl font-black text-slate-800 group-hover:text-blue-600 transition-colors">Rp {{ number_format($stats['avg_order_value'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Rata-rata per Pesanan</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-lg hover:shadow-slate-100/50 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Done</span>
            </div>
            <p class="text-3xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors">{{ number_format($stats['completed_orders']) }}</p>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Pesanan Selesai</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Tren Pendapatan</h3>
                <div class="flex gap-1">
                    <button onclick="switchRevenueChart('line')" data-type="line"
                        class="rev-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-amber-500 text-white transition-all">Line</button>
                    <button onclick="switchRevenueChart('bar')" data-type="bar"
                        class="rev-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Bar</button>
                    <button onclick="switchRevenueChart('area')" data-type="area"
                        class="rev-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Area</button>
                </div>
            </div>
            <div class="h-72"><canvas id="revenueChart"></canvas></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Menu Terlaris (Top 10)</h3>
                <div class="flex gap-1">
                    <button onclick="switchMenuChart('bar')" data-type="bar"
                        class="menu-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-500 text-white transition-all">Bar</button>
                    <button onclick="switchMenuChart('horizontalBar')" data-type="horizontalBar"
                        class="menu-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">H-Bar</button>
                    <button onclick="switchMenuChart('radar')" data-type="radar"
                        class="menu-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Radar</button>
                    <button onclick="switchMenuChart('polarArea')" data-type="polarArea"
                        class="menu-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Polar</button>
                </div>
            </div>
            <div class="h-72"><canvas id="topMenuChart"></canvas></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Status Pesanan</h3>
                <div class="flex gap-1">
                    <button onclick="switchStatusChart('doughnut')" data-type="doughnut"
                        class="status-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-purple-500 text-white transition-all">Donut</button>
                    <button onclick="switchStatusChart('pie')" data-type="pie"
                        class="status-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Pie</button>
                    <button onclick="switchStatusChart('bar')" data-type="bar"
                        class="status-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Bar</button>
                </div>
            </div>
            <div class="h-64"><canvas id="statusChart"></canvas></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Jam Tersibuk</h3>
                <div class="flex gap-1">
                    <button onclick="switchPeakChart('bar')" data-type="bar"
                        class="peak-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-500 text-white transition-all">Bar</button>
                    <button onclick="switchPeakChart('line')" data-type="line"
                        class="peak-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Line</button>
                </div>
            </div>
            <div class="h-64"><canvas id="peakHoursChart"></canvas></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Metode Pembayaran</h3>
                <div class="flex gap-1">
                    <button onclick="switchPaymentChart('doughnut')" data-type="doughnut"
                        class="pay-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-rose-500 text-white transition-all">Donut</button>
                    <button onclick="switchPaymentChart('pie')" data-type="pie"
                        class="pay-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Pie</button>
                    <button onclick="switchPaymentChart('bar')" data-type="bar"
                        class="pay-btn px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">Bar</button>
                </div>
            </div>
            <div class="h-64"><canvas id="paymentChart"></canvas></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Riwayat Transaksi</h3>
                    <p class="text-[10px] text-slate-400 font-medium">{{ $transactions->count() }} transaksi pada periode ini</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="pl-6 pr-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">No. Order</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Meja</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Item</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Total</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Pembayaran</th>
                        <th class="px-4 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Status</th>
                        <th class="pl-4 pr-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="pl-6 pr-4 py-4 font-bold text-slate-800">{{ $t->order_number }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">
                                {{ $t->table->number ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-slate-600 font-medium">{{ $t->items->count() }} item</td>
                        <td class="px-4 py-4 font-black text-emerald-600">{{ $t->formatted_total }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider
                                {{ $t->payment_method === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $t->payment_method === 'online' ? '💳 Online' : '💵 Kasir' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $sc = [
                                    'pending'         => 'bg-yellow-100 text-yellow-700',
                                    'processing'      => 'bg-blue-100 text-blue-700',
                                    'ready'           => 'bg-emerald-100 text-emerald-700',
                                    'completed'       => 'bg-slate-100 text-slate-600',
                                    'payment_success' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled'       => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider {{ $sc[$t->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $t->status_label }}
                            </span>
                        </td>
                        <td class="pl-4 pr-6 py-4 text-slate-400 font-medium">{{ $t->created_at->format('d/m H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <p class="text-slate-500 font-medium">Tidak ada transaksi pada periode ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Export helper: collects current filter params and opens export URL ──
window.exportReport = function(type) {
    const form = document.getElementById('report-form');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    const route = type === 'pdf'
        ? '{{ route('admin.reports.export-pdf') }}'
        : '{{ route('admin.reports.export-excel') }}';
    window.open(route + '?' + params.toString(), '_blank');
};

document.addEventListener('DOMContentLoaded', function () {

    // ── Data dari server ──────────────────────────────────────────────────
    const revData    = @json($revenuePerDay);
    const revMonData = @json($revenuePerMonth);
    const topData    = @json($topMenus);
    const statusData = @json($statusDistribution);
    const peakData   = @json($peakHours);
    const payData    = @json($paymentMethods);
    const isYearView = '{{ $range }}' === 'year';

    const COLORS = ['#f59e0b','#3b82f6','#10b981','#8b5cf6','#ef4444','#06b6d4','#84cc16','#f97316','#ec4899','#6366f1'];
    const STATUS_LABELS = {
        pending: 'Menunggu', processing: 'Diproses', ready: 'Siap',
        completed: 'Selesai', cancelled: 'Dibatalkan',
        payment_success: 'Bayar Sukses', pending_payment: 'Menunggu Bayar'
    };
    const STATUS_COLORS = {
        pending: '#eab308', processing: '#3b82f6', ready: '#10b981',
        completed: '#6b7280', cancelled: '#ef4444',
        payment_success: '#10b981', pending_payment: '#f59e0b'
    };
    const MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    function fmtDate(d) {
        return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
    }

    function setActiveBtn(selector, activeType, activeColorClass) {
        document.querySelectorAll(selector).forEach(function(b) {
            b.classList.remove(activeColorClass, 'text-white');
            b.classList.add('bg-slate-100', 'text-slate-500');
        });
        var active = document.querySelector(selector + '[data-type="' + activeType + '"]');
        if (active) {
            active.classList.remove('bg-slate-100', 'text-slate-500');
            active.classList.add(activeColorClass, 'text-white');
        }
    }

    // ── 1. Revenue Chart ──────────────────────────────────────────────────
    var revenueChart = null;
    var revLabels = isYearView
        ? revMonData.map(function(d) { return MONTHS[d.month - 1]; })
        : revData.map(function(d) { return fmtDate(d.date); });
    var revValues = isYearView
        ? revMonData.map(function(d) { return d.revenue; })
        : revData.map(function(d) { return d.revenue; });

    window.switchRevenueChart = function(type) {
        if (revenueChart) { revenueChart.destroy(); revenueChart = null; }
        var isArea = (type === 'area');
        var ct = isArea ? 'line' : type;
        revenueChart = new Chart(document.getElementById('revenueChart'), {
            type: ct,
            data: {
                labels: revLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: revValues,
                    borderColor: '#f59e0b',
                    backgroundColor: isArea ? 'rgba(245,158,11,0.15)' : (ct === 'bar' ? '#f59e0b' : 'rgba(245,158,11,0.08)'),
                    fill: isArea,
                    tension: 0.4,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: ct === 'bar' ? 0 : 4,
                    borderRadius: ct === 'bar' ? 6 : 0,
                    borderWidth: ct === 'bar' ? 0 : 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                return 'Rp ' + (v / 1000).toFixed(0) + 'K';
                            }
                        }
                    }
                }
            }
        });
        setActiveBtn('.rev-btn', type, 'bg-amber-500');
    };
    switchRevenueChart('line');

    // ── 2. Menu Chart ─────────────────────────────────────────────────────
    var menuChart = null;
    var menuLabels = topData.map(function(d) { return d.menu_item ? d.menu_item.name : 'Unknown'; });
    var menuValues = topData.map(function(d) { return d.total_qty; });

    window.switchMenuChart = function(type) {
        if (menuChart) { menuChart.destroy(); menuChart = null; }
        var isHBar = (type === 'horizontalBar');
        var ct = isHBar ? 'bar' : type;
        var isCircular = (type === 'radar' || type === 'polarArea');
        menuChart = new Chart(document.getElementById('topMenuChart'), {
            type: ct,
            data: {
                labels: menuLabels,
                datasets: [{
                    label: 'Terjual',
                    data: menuValues,
                    backgroundColor: COLORS.slice(0, menuLabels.length),
                    borderColor: isCircular ? COLORS : 'transparent',
                    borderWidth: isCircular ? 2 : 0,
                    borderRadius: (!isCircular && !isHBar) ? 6 : 0,
                    pointBackgroundColor: COLORS,
                }]
            },
            options: {
                indexAxis: isHBar ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: isCircular, position: 'bottom' } },
                scales: isCircular ? {} : { y: { beginAtZero: true } }
            }
        });
        setActiveBtn('.menu-btn', type, 'bg-blue-500');
    };
    switchMenuChart('bar');

    // ── 3. Status Chart ───────────────────────────────────────────────────
    var statusChart = null;

    window.switchStatusChart = function(type) {
        if (statusChart) { statusChart.destroy(); statusChart = null; }
        var isBar = (type === 'bar');
        statusChart = new Chart(document.getElementById('statusChart'), {
            type: type,
            data: {
                labels: statusData.map(function(s) { return STATUS_LABELS[s.status] || s.status; }),
                datasets: [{
                    data: statusData.map(function(s) { return s.count; }),
                    backgroundColor: statusData.map(function(s) { return STATUS_COLORS[s.status] || '#94a3b8'; }),
                    borderWidth: isBar ? 0 : 2,
                    borderColor: '#fff',
                    borderRadius: isBar ? 6 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: isBar ? 'top' : 'bottom',
                        labels: { padding: 12, font: { size: 11 } }
                    }
                },
                scales: isBar ? { y: { beginAtZero: true } } : {}
            }
        });
        setActiveBtn('.status-btn', type, 'bg-purple-500');
    };
    switchStatusChart('doughnut');

    // ── 4. Peak Hours Chart ───────────────────────────────────────────────
    var peakChart = null;

    window.switchPeakChart = function(type) {
        if (peakChart) { peakChart.destroy(); peakChart = null; }
        peakChart = new Chart(document.getElementById('peakHoursChart'), {
            type: type,
            data: {
                labels: peakData.map(function(h) { return h.hour + ':00'; }),
                datasets: [{
                    label: 'Pesanan',
                    data: peakData.map(function(h) { return h.count; }),
                    backgroundColor: type === 'bar' ? '#10b981' : 'rgba(16,185,129,0.15)',
                    borderColor: '#10b981',
                    borderWidth: type === 'line' ? 2 : 0,
                    tension: 0.4,
                    fill: type === 'line',
                    borderRadius: type === 'bar' ? 6 : 0,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: type === 'line' ? 4 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
        setActiveBtn('.peak-btn', type, 'bg-emerald-500');
    };
    switchPeakChart('bar');

    // ── 5. Payment Chart ──────────────────────────────────────────────────
    var paymentChart = null;

    window.switchPaymentChart = function(type) {
        if (paymentChart) { paymentChart.destroy(); paymentChart = null; }
        var isBar = (type === 'bar');
        paymentChart = new Chart(document.getElementById('paymentChart'), {
            type: type,
            data: {
                labels: payData.map(function(p) { return p.payment_method === 'online' ? '💳 Online' : '💵 Kasir'; }),
                datasets: [{
                    data: payData.map(function(p) { return p.count; }),
                    backgroundColor: ['#3b82f6', '#f59e0b', '#10b981', '#8b5cf6'],
                    borderWidth: isBar ? 0 : 2,
                    borderColor: '#fff',
                    borderRadius: isBar ? 6 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: isBar ? 'top' : 'bottom',
                        labels: { padding: 12, font: { size: 11 } }
                    }
                },
                scales: isBar ? { y: { beginAtZero: true } } : {}
            }
        });
        setActiveBtn('.pay-btn', type, 'bg-rose-500');
    };
    switchPaymentChart('doughnut');

    // ── Flatpickr untuk custom date ───────────────────────────────────────
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#start-date, #end-date', {
            locale: 'id',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            allowInput: true
        });
    }
});
</script>
@endpush
@endsection
