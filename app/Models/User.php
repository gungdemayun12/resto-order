<?php

namespace App\Models;


use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isKitchen(): bool
    {
        return $this->role === 'kitchen';
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'owner' => 'Pemilik',
            'cashier' => 'Kasir',
            'kitchen' => 'Dapur',
            default => ucfirst($this->role),
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->roleLabel();
    }

    public static function defaultPermissions(?string $role): array
    {
        return match ($role) {
            'owner' => [
                'view_dashboard',
                'view_orders',
                'scan_orders',
                'view_tables',
                'view_sessions',
                'view_reservations',
                'view_reports',
                'manage_menu',
                'manage_users',
            ],
            'cashier' => [
                'view_dashboard',
                'view_orders',
                'view_tables',
                'view_sessions',
                'view_reservations',
                'scan_orders',
            ],
            'kitchen' => [
                'view_orders',
                'view_sessions',
            ],
            default => [],
        };
    }

    public function getEffectivePermissionsAttribute(): array
    {
        if (!is_null($this->permissions)) {
            return array_values(array_unique($this->permissions));
        }

        return self::defaultPermissions($this->role);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return in_array($permission, $this->effective_permissions, true);
    }

    public function canScanOrders(): bool
    {
        return $this->isOwner()
            || $this->isCashier()
            || $this->hasPermission('scan_orders');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
}

