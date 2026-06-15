<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::query();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('search')) {
            $searchTerm = strtolower(str_replace(' ', '', $request->search));
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("LOWER(REPLACE(name, ' ', '')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(REPLACE(phone, ' ', '')) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        $reservations = $query->orderBy('date', 'desc')
            ->orderBy('time', 'asc')
            ->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,rejected',
        ]);

        $reservation->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'confirmed'
                    ? 'Reservasi berhasil dikonfirmasi!'
                    : 'Reservasi berhasil ditolak.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Status reservasi berhasil diperbarui!');
    }
}