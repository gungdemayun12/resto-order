<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TableSession;

class ValidateTableSession
{
    




    public function handle(Request $request, Closure $next): Response
    {
        
        $sessionToken = $request->query('session') ?? $request->input('session_token');

        if (!$sessionToken) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session token tidak ditemukan',
                ], 401);
            }

            return redirect()->route('customer.error')
                ->with('error', 'Session token tidak ditemukan');
        }

        
        $session = TableSession::where('session_token', $sessionToken)->first();

        if (!$session) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak valid',
                ], 404);
            }

            return view('customer.error', [
                'title' => 'Session Tidak Valid',
                'message' => 'Session Anda tidak ditemukan. Silakan scan QR code lagi.',
            ]);
        }

        
        if (!$session->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session sudah tidak aktif atau expired',
                    'expired' => true,
                ], 403);
            }

            return view('customer.error', [
                'title' => 'Session Expired',
                'message' => 'Session Anda sudah tidak aktif. Silakan scan QR code lagi untuk memulai session baru.',
            ]);
        }

        
        $request->merge(['table_session' => $session]);

        return $next($request);
    }
}
