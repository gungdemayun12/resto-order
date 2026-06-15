<?php

namespace App\Http\Middleware;

use App\Models\RestaurantTable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerSession
{
    



    public function handle(Request $request, Closure $next): Response
    {
        $tableNumber = session('customer_table');
        $customerName = session('customer_name');
        $customerPhone = session('customer_phone');

        
        if (!$tableNumber || !$customerName || !$customerPhone) {
            
            $meja = $tableNumber ?? $request->input('meja', '');

            return redirect()->route('menu.start', $meja ? ['meja' => $meja] : [])
                ->with('warning', 'Silakan isi data diri Anda terlebih dahulu.');
        }

        
        $table = RestaurantTable::where('number', $tableNumber)->first();
        if (!$table) {
            session()->forget(['customer_table', 'customer_name', 'customer_phone']);
            return redirect()->route('menu.start')
                ->with('error', 'Nomor meja tidak ditemukan. Silakan scan QR Code kembali.');
        }

        
        $request->merge(['_table' => $table]);

        return $next($request);
    }
}
