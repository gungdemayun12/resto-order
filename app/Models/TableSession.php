<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TableSession extends Model
{
    protected $dispatchesEvents = [
        'created' => \App\Events\DashboardUpdated::class,
        'updated' => \App\Events\DashboardUpdated::class,
        'deleted' => \App\Events\DashboardUpdated::class,
    ];
    protected $fillable = [
        'table_id',
        'session_token',
        'qr_scan_token',
        'status',
        'started_at',
        'expires_at',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'session_token', 'session_token');
    }

    public static function generateSessionToken(): string
    {
        return 'SES-' . strtoupper(Str::random(16));
    }

    public static function generateQrScanToken(): string
    {
        return 'QR-' . strtoupper(Str::random(20));
    }

    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->expires_at > now();
    }

    public function isExpired(): bool
    {
        return $this->expires_at <= now() || $this->status === 'expired';
    }

    public function close(string $closedBy = 'admin'): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => $closedBy,
        ]);

        $this->table()->update([
            'status' => 'available',
        ]);
    }

    public function expire(): void
    {
        $this->update([
            'status' => 'expired',
            'closed_at' => now(),
            'closed_by' => 'system',
        ]);

        $this->table()->update([
            'status' => 'available',
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '<=', now());
    }

    public static function getActiveSessionForTable(int $tableId): ?self
    {
        return self::where('table_id', $tableId)
            ->active()
            ->first();
    }

    public static function createForTable(int $tableId, int $durationHours = 3): self
    {
        $oldSession = self::getActiveSessionForTable($tableId);
        if ($oldSession) {
            $oldSession->close('system');
        }

        $session = self::create([
            'table_id' => $tableId,
            'session_token' => self::generateSessionToken(),
            'qr_scan_token' => self::generateQrScanToken(),
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addHours($durationHours),
        ]);

        RestaurantTable::find($tableId)->update(['status' => 'occupied']);

        return $session;
    }
}
