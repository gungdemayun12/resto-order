<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        User::query()->each(function (User $user) {
            if (! in_array($user->role, ['owner', 'cashier'], true)) {
                return;
            }

            $permissions = is_array($user->permissions)
                ? $user->permissions
                : User::defaultPermissions($user->role);

            if (! in_array('scan_orders', $permissions, true)) {
                $permissions[] = 'scan_orders';
                $user->update(['permissions' => array_values(array_unique($permissions))]);
            }
        });
    }

    public function down(): void
    {
        // Tidak perlu rollback — scan_orders aman dipertahankan.
    }
};
