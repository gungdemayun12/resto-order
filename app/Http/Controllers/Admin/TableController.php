<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $query = RestaurantTable::withCount('activeOrders')->orderBy('number');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', $request->capacity);
        }

        if ($request->filled('search')) {
            $searchTerm = strtolower(str_replace(' ', '', $request->search));
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("LOWER(REPLACE(CAST(number AS CHAR), ' ', '')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(REPLACE(location, ' ', '')) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        $tables = $query->get();

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|integer|unique:restaurant_tables,number',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'required|in:indoor,outdoor,vip',
        ]);

        $validated['qr_token'] = Str::uuid()->toString();

        RestaurantTable::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Meja berhasil ditambahkan!']);
        }

        return role_redirect('admin.tables.index')
            ->with('success', 'Meja berhasil ditambahkan!');
    }

    public function updateStatus(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status meja berhasil diperbarui!',
            ]);
        }

        return role_redirect('admin.tables.index')
            ->with('success', 'Status meja berhasil diperbarui!');
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'number' => 'required|integer|unique:restaurant_tables,number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'required|in:indoor,outdoor,vip',
        ]);

        $table->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Meja berhasil diperbarui!']);
        }

        return role_redirect('admin.tables.index')
            ->with('success', 'Meja berhasil diperbarui!');
    }

    public function generateQR(RestaurantTable $table)
    {
        $url = url('/menu?meja=' . $table->number);
        return view('admin.tables.qr', compact('table', 'url'));
    }

    public function destroy(Request $request, RestaurantTable $table)
    {
        $table->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Meja berhasil dihapus!']);
        }

        return role_redirect('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus!');
    }
}