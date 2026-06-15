<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    protected $table = 'restaurant_tables';

    protected $fillable = [
        'number',
        'capacity',
        'location',
        'status',
        'qr_token',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function activeSession()
    {
        return $this->hasOne(TableSession::class, 'table_id')->where('status', 'active')->where('expires_at', '>', now());
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TableSession::class, 'table_id');
    }

    public function activeOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id')
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function getLocationLabelAttribute(): string
    {
        return match ($this->location) {
            'indoor' => 'Indoor',
            'outdoor' => 'Outdoor',
            'vip' => 'VIP',
            default => $this->location,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'Tersedia',
            'occupied' => 'Terisi',
            'reserved' => 'Dipesan',
            default => $this->status,
        };
    }
}
