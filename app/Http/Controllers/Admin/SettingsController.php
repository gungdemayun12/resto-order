<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        $availablePermissions = User::defaultPermissions('owner');

        return view('admin.settings', compact('users', 'availablePermissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:owner,cashier,kitchen',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        if ($user->id === $request->user()->id && $request->role !== 'owner') {
            $message = 'Anda tidak dapat mengubah peran diri sendiri menjadi non-owner.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        $permissions = $request->input('permissions', []);

        $user->update([
            'role' => $request->role,
            'permissions' => $permissions,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hak akses pengguna berhasil diperbarui.',
            ]);
        }

        return redirect()->back()->with('success', 'Hak akses pengguna berhasil diperbarui.');
    }
}
