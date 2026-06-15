<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    private function getReportData(Request $request): array
    {
        $range = $request->input('range', 'week');
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year', now()->year);

        if ($range === 'month_picker' && $selectedMonth) {
            $startDate = Carbon::parse($selectedMonth . '-01')->startOfMonth();
            $endDate = Carbon::parse($selectedMonth . '-01')->endOfMonth();
        } elseif ($range === 'year') {
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
        } elseif ($range === 'custom') {
            $startDate = Carbon::parse($request->input('start_date', Carbon::now()->subDays(7)));
            $endDate = Carbon::parse($request->input('end_date', Carbon::today()))->endOfDay();
        } else {
            [$startDate, $endDate] = match ($range) {
                'today'   => [Carbon::today(), Carbon::now()->endOfDay()],
                'week'    => [Carbon::now()->subDays(7), Carbon::now()->endOfDay()],
                'month'   => [Carbon::now()->subDays(30), Carbon::now()->endOfDay()],
                'quarter' => [Carbon::now()->subMonths(3), Carbon::now()->endOfDay()],
                default   => [Carbon::now()->subDays(7), Carbon::now()->endOfDay()],
            };
        }

        $stats = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotIn('status', ['cancelled'])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['processing', 'ready', 'completed'])->sum('total_amount'),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['processing', 'ready', 'completed'])->avg('total_amount') ?? 0,
            'completed_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
        ];

        $revenuePerDay = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['processing', 'ready', 'completed'])
            ->groupBy(DB::raw('DATE(created_at)'))->orderBy('date')->get();

        $revenuePerMonth = Order::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['processing', 'ready', 'completed'])
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year')->orderBy('month')->get();

        $topMenus = OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->whereNotIn('status', ['cancelled']);
            })
            ->groupBy('menu_item_id')->orderByDesc('total_qty')->limit(10)->with('menuItem')->get();

        $statusDistribution = Order::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])->groupBy('status')->get();

        $peakHours = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$startDate, $endDate])->whereNotIn('status', ['cancelled'])
            ->groupBy(DB::raw('HOUR(created_at)'))->orderBy('hour')->get();

        $paymentMethods = Order::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['processing', 'ready', 'completed'])->groupBy('payment_method')->get();

        $transactions = Order::with(['table', 'items.menuItem'])
            ->whereBetween('created_at', [$startDate, $endDate])->whereNotIn('status', ['cancelled'])
            ->orderBy('created_at', 'desc')->limit(100)->get();

        $availableYears = Order::selectRaw('YEAR(created_at) as year')
            ->groupBy(DB::raw('YEAR(created_at)'))->orderByDesc('year')->pluck('year');

        return compact(
            'stats', 'revenuePerDay', 'revenuePerMonth', 'topMenus',
            'statusDistribution', 'peakHours', 'paymentMethods',
            'transactions', 'range', 'startDate', 'endDate',
            'selectedMonth', 'selectedYear', 'availableYears'
        );
    }

    public function index(Request $request)
    {
        $data = $this->getReportData($request);

        if ($request->wantsJson()) {
            return response()->json([
                'stats' => $data['stats'],
                'revenue_per_day' => $data['revenuePerDay'],
                'revenue_per_month' => $data['revenuePerMonth'],
                'top_menus' => $data['topMenus'],
                'status_distribution' => $data['statusDistribution'],
                'peak_hours' => $data['peakHours'],
                'payment_methods' => $data['paymentMethods'],
            ]);
        }

        return view('admin.reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        $data['periodLabel'] = $data['startDate']->translatedFormat('d F Y') . ' – ' . $data['endDate']->translatedFormat('d F Y');
        $data['generatedAt'] = now()->translatedFormat('d F Y, H:i') . ' WIB';
        $data['logoPath'] = public_path('storage/logo.png');

        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'sans-serif');

        $filename = 'Laporan_Pendapatan_' . $data['startDate']->format('Y-m-d') . '_' . $data['endDate']->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData($request);
        $periodLabel = $data['startDate']->translatedFormat('d F Y') . ' – ' . $data['endDate']->translatedFormat('d F Y');

        $spreadsheet = new Spreadsheet();

        // ── Colors ──
        $amber     = 'F59E0B';
        $amberDark = 'D97706';
        $dark      = '1E293B';
        $slate     = '64748B';
        $white     = 'FFFFFF';
        $lightBg   = 'F8FAFC';

        // ── Styles ──
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => $dark]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $subtitleStyle = [
            'font' => ['size' => 11, 'color' => ['rgb' => $slate]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ];
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $white]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $dark]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $dark]]],
        ];
        $dataStyle = [
            'font' => ['size' => 10, 'color' => ['rgb' => $dark]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ];
        $currencyStyle = array_replace_recursive($dataStyle, [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0'],
        ]);
        $statLabelStyle = [
            'font' => ['size' => 10, 'color' => ['rgb' => $slate]],
        ];
        $statValueStyle = [
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $amberDark]],
        ];
        $sectionTitleStyle = [
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $dark]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];

        // ═══════════════════════════════════════════════════
        // SHEET 1: RINGKASAN
        // ═══════════════════════════════════════════════════
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan');

        // Header
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN PENDAPATAN – RESTONUSA');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);
        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Periode: ' . $periodLabel);
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', 'Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A3')->applyFromArray($subtitleStyle);

        // Stats
        $row = 5;
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'RINGKASAN STATISTIK');
        $sheet->getStyle("A{$row}")->applyFromArray($sectionTitleStyle);
        $row++;

        $statsRows = [
            ['Total Pesanan', number_format($data['stats']['total_orders']), ''],
            ['Total Pendapatan', $data['stats']['total_revenue'], 'Rp '],
            ['Rata-rata per Pesanan', round($data['stats']['avg_order_value']), 'Rp '],
            ['Pesanan Selesai', number_format($data['stats']['completed_orders']), ''],
        ];
        foreach ($statsRows as $s) {
            $sheet->setCellValue("A{$row}", $s[0]);
            $sheet->getStyle("A{$row}")->applyFromArray($statLabelStyle);
            $sheet->setCellValue("C{$row}", $s[2] . number_format($s[1], 0, ',', '.'));
            $sheet->getStyle("C{$row}")->applyFromArray($statValueStyle);
            $row++;
        }

        // Revenue per Day
        $row += 1;
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'PENDAPATAN PER HARI');
        $sheet->getStyle("A{$row}")->applyFromArray($sectionTitleStyle);
        $row++;

        $sheet->setCellValue("A{$row}", 'Tanggal');
        $sheet->setCellValue("B{$row}", 'Pendapatan (Rp)');
        $sheet->setCellValue("C{$row}", 'Jumlah Order');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;

        foreach ($data['revenuePerDay'] as $d) {
            $sheet->setCellValue("A{$row}", Carbon::parse($d->date)->translatedFormat('d F Y'));
            $sheet->setCellValue("B{$row}", $d->revenue);
            $sheet->setCellValue("C{$row}", $d->orders);
            $sheet->getStyle("A{$row}")->applyFromArray($dataStyle);
            $sheet->getStyle("B{$row}")->applyFromArray($currencyStyle);
            $sheet->getStyle("C{$row}")->applyFromArray(array_replace_recursive($dataStyle, ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]));
            if (($row % 2) === 0) {
                $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($lightBg);
            }
            $row++;
        }

        // Top Menu
        $row += 1;
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'MENU TERLARIS (TOP 10)');
        $sheet->getStyle("A{$row}")->applyFromArray($sectionTitleStyle);
        $row++;

        $sheet->setCellValue("A{$row}", 'No');
        $sheet->setCellValue("B{$row}", 'Menu');
        $sheet->setCellValue("C{$row}", 'Qty Terjual');
        $sheet->setCellValue("D{$row}", 'Pendapatan (Rp)');
        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($row)->setRowHeight(25);
        $row++;

        foreach ($data['topMenus'] as $i => $m) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $m->menuItem->name ?? '-');
            $sheet->setCellValue("C{$row}", $m->total_qty);
            $sheet->setCellValue("D{$row}", $m->total_revenue);
            $sheet->getStyle("A{$row}")->applyFromArray(array_replace_recursive($dataStyle, ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]));
            $sheet->getStyle("B{$row}")->applyFromArray($dataStyle);
            $sheet->getStyle("C{$row}")->applyFromArray(array_replace_recursive($dataStyle, ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]));
            $sheet->getStyle("D{$row}")->applyFromArray($currencyStyle);
            $row++;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        // ═══════════════════════════════════════════════════
        // SHEET 2: RIWAYAT TRANSAKSI
        // ═══════════════════════════════════════════════════
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Riwayat Transaksi');

        $sheet2->mergeCells('A1:G1');
        $sheet2->setCellValue('A1', 'RIWAYAT TRANSAKSI');
        $sheet2->getStyle('A1')->applyFromArray($titleStyle);
        $sheet2->getRowDimension(1)->setRowHeight(30);

        $sheet2->mergeCells('A2:G2');
        $sheet2->setCellValue('A2', 'Periode: ' . $periodLabel);
        $sheet2->getStyle('A2')->applyFromArray($subtitleStyle);

        $row = 4;
        $headers = ['No. Order', 'Meja', 'Pelanggan', 'Item', 'Total (Rp)', 'Pembayaran', 'Status', 'Waktu'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet2->setCellValue("{$col}{$row}", $h);
        }
        $sheet2->getStyle("A{$row}:H{$row}")->applyFromArray($headerStyle);
        $sheet2->getRowDimension($row)->setRowHeight(25);
        $row++;

        foreach ($data['transactions'] as $t) {
            $sheet2->setCellValue("A{$row}", $t->order_number);
            $sheet2->setCellValue("B{$row}", $t->table->number ?? '-');
            $sheet2->setCellValue("C{$row}", $t->customer_name ?? '-');
            $sheet2->setCellValue("D{$row}", $t->items->count() . ' item');
            $sheet2->setCellValue("E{$row}", $t->total_amount);
            $sheet2->setCellValue("F{$row}", $t->payment_method === 'online' ? 'Online' : 'Kasir');
            $sheet2->setCellValue("G{$row}", $t->status_label);
            $sheet2->setCellValue("H{$row}", $t->created_at->translatedFormat('d M Y, H:i'));
            $sheet2->getStyle("A{$row}:H{$row}")->applyFromArray($dataStyle);
            $sheet2->getStyle("E{$row}")->applyFromArray($currencyStyle);
            $row++;
        }

        $sheet2->getColumnDimension('A')->setWidth(18);
        $sheet2->getColumnDimension('B')->setWidth(8);
        $sheet2->getColumnDimension('C')->setWidth(20);
        $sheet2->getColumnDimension('D')->setWidth(10);
        $sheet2->getColumnDimension('E')->setWidth(18);
        $sheet2->getColumnDimension('F')->setWidth(14);
        $sheet2->getColumnDimension('G')->setWidth(16);
        $sheet2->getColumnDimension('H')->setWidth(20);

        // ── Download ──
        $filename = 'Laporan_Pendapatan_' . $data['startDate']->format('Y-m-d') . '_' . $data['endDate']->format('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
