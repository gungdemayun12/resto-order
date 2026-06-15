<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $employees = $query->orderBy('role')->orderBy('name')->paginate(12);
        $roleStats = [
            'all' => User::count(),
            'owner' => User::where('role', 'owner')->count(),
            'cashier' => User::where('role', 'cashier')->count(),
            'kitchen' => User::where('role', 'kitchen')->count(),
        ];

        return view('admin.employees.index', compact('employees', 'roleStats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:owner,cashier,kitchen',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? null,
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'system',
            'title' => 'Selamat Datang!',
            'body' => 'Akun Anda telah dibuat sebagai ' . $user->roleLabel() . '. Selamat bekerja!',
            'icon' => 'check',
            'color' => 'emerald',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan!',
                'user' => $user,
            ]);
        }

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:owner,cashier,kitchen',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui!',
            ]);
        }

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy(Request $request, User $employee)
    {
        if ($employee->id === $request->user()->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun sendiri.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($employee->isOwner() && User::where('role', 'owner')->count() <= 1) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus pemilik terakhir.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Tidak dapat menghapus pemilik terakhir.');
        }

        $employee->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus!',
            ]);
        }

        return redirect()->back()->with('success', 'Karyawan berhasil dihapus!');
    }
}
