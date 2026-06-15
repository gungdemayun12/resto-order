<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\TableSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QrScanController extends Controller
{

    public function scan($qrToken)
    {
        try {

            $table = RestaurantTable::where('qr_token', $qrToken)->first();

            if (!$table) {
                return view('customer.error', [
                    'title' => 'QR Code Tidak Valid',
                    'message' => 'QR code yang Anda scan tidak terdaftar dalam sistem.',
                ]);
            }

            if ($table->status === 'reserved') {
                return view('customer.error', [
                    'title' => 'Meja Telah Direservasi',
                    'message' => 'Maaf, meja ini sudah dipesan melalui reservasi. Silakan hubungi staf kami.',
                ]);
            }

            $activeSession = TableSession::getActiveSessionForTable($table->id);

            if ($activeSession) {

                return redirect()->route('customer.form', [
                    'session' => $activeSession->session_token
                ]);
            }

            if ($table->status === 'occupied' && !$activeSession) {

                $table->update(['status' => 'available']);
            }

            if ($table->status !== 'available') {
                return view('customer.error', [
                    'title' => 'Meja Tidak Tersedia',
                    'message' => 'Meja ini sedang digunakan atau sedang dalam pembersihan.',
                ]);
            }

            $session = TableSession::createForTable($table->id, 3);

            $table->update(['status' => 'occupied']);

            Log::info('New table session created', [
                'table_id' => $table->id,
                'table_number' => $table->number,
                'session_token' => $session->session_token,
            ]);

            return redirect()->route('customer.form', [
                'session' => $session->session_token
            ]);

        } catch (\Exception $e) {
            Log::error('QR Scan Error: ' . $e->getMessage());

            return view('customer.error', [
                'title' => 'Terjadi Kesalahan',
                'message' => 'Maaf, terjadi kesalahan saat memproses QR code. Silakan coba lagi.',
            ]);
        }
    }

    public function showForm(Request $request)
    {
        $sessionToken = $request->query('session');

        $session = TableSession::where('session_token', $sessionToken)
            ->with('table')
            ->first();

        if (!$session) {
            return view('customer.error', [
                'title' => 'Session Tidak Valid',
                'message' => 'Session Anda tidak ditemukan. Silakan scan QR code lagi.',
            ]);
        }

        if (!$session->isActive()) {
            return view('customer.session-ended');
        }

        $customerData = session()->get('customer_data_' . $sessionToken);
        if ($customerData) {

            return redirect()->route('customer.menu', ['session' => $sessionToken]);
        }

        return view('customer.form', [
            'session' => $session,
            'tableNumber' => $session->table->number,
        ]);
    }

    public function storeCustomerData(Request $request)
    {
        $request->validate([
            'session_token' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:10|max:15',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ], [
            'customer_name.required' => 'Nama harus diisi',
            'customer_phone.required' => 'Nomor HP harus diisi',
            'customer_phone.min' => 'Nomor HP minimal 10 digit',
        ]);

        $sessionToken = $request->session_token;

        $session = TableSession::where('session_token', $sessionToken)->first();
        if (!$session || !$session->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak valid atau sudah expired',
            ], 403);
        }

        session()->put('customer_data_' . $sessionToken, [
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'redirect' => route('customer.menu', ['session' => $sessionToken]),
        ]);
    }

    public function validateSession(Request $request)
    {
        $sessionToken = $request->input('session_token');

        $session = TableSession::where('session_token', $sessionToken)
            ->with('table')
            ->first();

        if (!$session) {
            return response()->json([
                'valid' => false,
                'message' => 'Session tidak ditemukan',
            ], 404);
        }

        if (!$session->isActive()) {
            return response()->json([
                'valid' => false,
                'message' => 'Session sudah tidak aktif atau expired',
                'expired' => true,
            ], 403);
        }

        return response()->json([
            'valid' => true,
            'session' => [
                'token' => $session->session_token,
                'table_number' => $session->table->number,
                'expires_at' => $session->expires_at->format('Y-m-d H:i:s'),
                'remaining_minutes' => now()->diffInMinutes($session->expires_at),
            ],
        ]);
    }
}