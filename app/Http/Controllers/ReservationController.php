<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return view('customer.reservation.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'guests' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'date.required' => 'Tanggal reservasi wajib diisi.',
            'date.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
            'time.required' => 'Waktu reservasi wajib diisi.',
            'guests.required' => 'Jumlah tamu wajib diisi.',
            'guests.min' => 'Minimal 1 tamu.',
            'guests.max' => 'Maksimal 20 tamu.',
        ]);

        $validated['reservation_code'] = Reservation::generateCode();
        $validated['status'] = 'pending';

        $reservation = Reservation::create($validated);

        return response()->json([
            'success' => true,
            'reservation' => $reservation,
            'message' => 'Reservasi berhasil dibuat! Kode reservasi Anda: ' . $reservation->reservation_code,
        ]);
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $reservations = Reservation::where('phone', $request->phone)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function ($r) {
                return [
                    'id' => $r->id,
                    'reservation_code' => $r->reservation_code,
                    'name' => $r->name,
                    'date' => $r->formatted_date,
                    'time' => $r->formatted_time,
                    'guests' => $r->guests,
                    'status' => $r->status,
                    'status_label' => $r->status_label,
                    'status_color' => $r->status_color,
                ];
            }),
        ]);
    }
}
