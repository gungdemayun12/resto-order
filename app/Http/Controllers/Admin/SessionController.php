<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableSession;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessionController extends Controller
{

    public function index()
    {
        $activeSessions = TableSession::with(['table', 'orders'])
            ->active()
            ->orderBy('started_at', 'desc')
            ->get();

        $expiredSessions = TableSession::with(['table', 'orders'])
            ->where('status', 'expired')
            ->orderBy('closed_at', 'desc')
            ->limit(20)
            ->get();

        $closedSessions = TableSession::with(['table', 'orders'])
            ->where('status', 'closed')
            ->orderBy('closed_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.sessions.index', [
            'activeSessions' => $activeSessions,
            'expiredSessions' => $expiredSessions,
            'closedSessions' => $closedSessions,
        ]);
    }

    public function close(Request $request, $id)
    {
        try {
            $session = TableSession::findOrFail($id);

            if ($session->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session sudah tidak aktif',
                ], 400);
            }

            $pendingOrders = $session->orders()
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();

            if ($pendingOrders > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada order yang belum selesai. Selesaikan order terlebih dahulu.',
                    'pending_orders' => $pendingOrders,
                ], 400);
            }

            $unpaidOrders = $session->orders()
                ->where('payment_status', 'unpaid')
                ->count();

            if ($unpaidOrders > 0 && !$request->force) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada order yang belum dibayar.',
                    'unpaid_orders' => $unpaidOrders,
                    'require_confirmation' => true,
                ], 400);
            }

            $session->close('admin');

            Log::info('Session closed by admin', [
                'session_id' => $session->id,
                'table_id' => $session->table_id,
                'admin' => auth()->user()->name ?? 'Unknown',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session berhasil ditutup',
            ]);

        } catch (\Exception $e) {
            Log::error('Close Session Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menutup session',
            ], 500);
        }
    }

    public function expireOldSessions()
    {
        try {
            $expiredSessions = TableSession::expired()->get();

            foreach ($expiredSessions as $session) {
                $session->expire();

                Log::info('Session auto-expired', [
                    'session_id' => $session->id,
                    'table_id' => $session->table_id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => count($expiredSessions) . ' session expired',
                'count' => count($expiredSessions),
            ]);

        } catch (\Exception $e) {
            Log::error('Expire Sessions Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error expiring sessions',
            ], 500);
        }
    }

    public function regenerateQr($tableId)
    {
        try {
            $table = RestaurantTable::findOrFail($tableId);

            $table->qr_token = \Illuminate\Support\Str::random(32);
            $table->save();

            Log::info('QR code regenerated', [
                'table_id' => $table->id,
                'table_number' => $table->number,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'QR code berhasil di-generate ulang',
                'qr_token' => $table->qr_token,
                'qr_url' => route('qr.scan', ['qrToken' => $table->qr_token]),
            ]);

        } catch (\Exception $e) {
            Log::error('Regenerate QR Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
            ], 500);
        }
    }

    public function show($id)
    {
        $session = TableSession::with(['table', 'orders.items.menuItem'])
            ->findOrFail($id);

        return view('admin.sessions.show', [
            'session' => $session,
        ]);
    }
}