<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'table_id',
        'session_token',
        'snap_token',
        'customer_name',
        'customer_phone',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'notes',
        'subtotal',
        'tax',
        'total_amount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'payment_success' => 'Pembayaran Berhasil',
            'pending_confirmation' => 'Menunggu Konfirmasi Kasir',
            'unconfirmed' => 'Menunggu Kasir',
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'ready' => 'Siap Disajikan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending_payment' => 'yellow',
            'payment_success' => 'green',
            'pending_confirmation' => 'orange',
            'pending' => 'yellow',
            'processing' => 'blue',
            'ready' => 'green',
            'completed' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public static function statusTheme(string $status): array
    {
        return self::statusThemes()[$status] ?? self::statusThemes()['pending'];
    }

    public static function statusThemes(): array
    {
        return [
            'pending_payment' => [
                'label' => 'Menunggu Pembayaran',
                'icon' => 'wallet',
                'gradient' => 'from-yellow-500 to-amber-600',
                'gradient_shadow' => 'shadow-yellow-900/10',
                'card_border' => 'border-yellow-200',
                'card_bg' => 'bg-yellow-50',
                'icon_wrap_bg' => 'bg-yellow-100',
                'icon_text' => 'text-yellow-600',
                'step_bg' => 'bg-yellow-500',
                'step_shadow' => 'shadow-yellow-500/30',
                'connector' => 'bg-yellow-400',
                'label_active' => 'text-yellow-700',
                'desc_active' => 'text-yellow-600',
                'landing_border' => 'border-yellow-200',
                'landing_shadow' => 'shadow-yellow-500/10',
                'landing_icon_bg' => 'bg-yellow-500',
                'landing_icon_shadow' => 'shadow-yellow-500/25',
                'landing_accent' => 'text-yellow-600',
                'landing_status' => 'text-yellow-600',
                'landing_hover_border' => 'hover:border-yellow-400',
                'landing_hover_arrow' => 'group-hover:text-yellow-500',
            ],
            'payment_success' => [
                'label' => 'Pembayaran Berhasil',
                'icon' => 'currency',
                'gradient' => 'from-emerald-500 to-green-600',
                'gradient_shadow' => 'shadow-emerald-900/10',
                'card_border' => 'border-emerald-200',
                'card_bg' => 'bg-emerald-50',
                'icon_wrap_bg' => 'bg-emerald-100',
                'icon_text' => 'text-emerald-600',
                'step_bg' => 'bg-emerald-500',
                'step_shadow' => 'shadow-emerald-500/30',
                'connector' => 'bg-emerald-400',
                'label_active' => 'text-emerald-700',
                'desc_active' => 'text-emerald-600',
                'landing_border' => 'border-emerald-200',
                'landing_shadow' => 'shadow-emerald-500/10',
                'landing_icon_bg' => 'bg-emerald-500',
                'landing_icon_shadow' => 'shadow-emerald-500/25',
                'landing_accent' => 'text-emerald-600',
                'landing_status' => 'text-emerald-600',
                'landing_hover_border' => 'hover:border-emerald-400',
                'landing_hover_arrow' => 'group-hover:text-emerald-500',
            ],
            'pending_confirmation' => [
                'label' => 'Menunggu Konfirmasi Kasir',
                'icon' => 'qr',
                'gradient' => 'from-orange-500 to-amber-600',
                'gradient_shadow' => 'shadow-orange-900/10',
                'card_border' => 'border-orange-200',
                'card_bg' => 'bg-orange-50',
                'icon_wrap_bg' => 'bg-orange-100',
                'icon_text' => 'text-orange-600',
                'step_bg' => 'bg-orange-500',
                'step_shadow' => 'shadow-orange-500/30',
                'connector' => 'bg-orange-400',
                'label_active' => 'text-orange-700',
                'desc_active' => 'text-orange-600',
                'landing_border' => 'border-orange-200',
                'landing_shadow' => 'shadow-orange-500/10',
                'landing_icon_bg' => 'bg-orange-500',
                'landing_icon_shadow' => 'shadow-orange-500/25',
                'landing_accent' => 'text-orange-600',
                'landing_status' => 'text-orange-600',
                'landing_hover_border' => 'hover:border-orange-400',
                'landing_hover_arrow' => 'group-hover:text-orange-500',
            ],
            'pending' => [
                'label' => 'Menunggu Konfirmasi',
                'icon' => 'clock',
                'gradient' => 'from-orange-500 to-orange-600',
                'gradient_shadow' => 'shadow-orange-900/10',
                'card_border' => 'border-orange-200',
                'card_bg' => 'bg-orange-50',
                'icon_wrap_bg' => 'bg-orange-100',
                'icon_text' => 'text-orange-600',
                'step_bg' => 'bg-orange-500',
                'step_shadow' => 'shadow-orange-500/30',
                'connector' => 'bg-orange-400',
                'label_active' => 'text-orange-700',
                'desc_active' => 'text-orange-600',
                'landing_border' => 'border-orange-200',
                'landing_shadow' => 'shadow-orange-500/10',
                'landing_icon_bg' => 'bg-orange-500',
                'landing_icon_shadow' => 'shadow-orange-500/25',
                'landing_accent' => 'text-orange-600',
                'landing_status' => 'text-orange-600',
                'landing_hover_border' => 'hover:border-orange-400',
                'landing_hover_arrow' => 'group-hover:text-orange-500',
            ],
            'processing' => [
                'label' => 'Sedang Diproses',
                'icon' => 'fire',
                'gradient' => 'from-blue-500 to-blue-600',
                'gradient_shadow' => 'shadow-blue-900/10',
                'card_border' => 'border-blue-200',
                'card_bg' => 'bg-blue-50',
                'icon_wrap_bg' => 'bg-blue-100',
                'icon_text' => 'text-blue-600',
                'step_bg' => 'bg-blue-500',
                'step_shadow' => 'shadow-blue-500/30',
                'connector' => 'bg-blue-400',
                'label_active' => 'text-blue-700',
                'desc_active' => 'text-blue-600',
                'landing_border' => 'border-blue-200',
                'landing_shadow' => 'shadow-blue-500/10',
                'landing_icon_bg' => 'bg-blue-500',
                'landing_icon_shadow' => 'shadow-blue-500/25',
                'landing_accent' => 'text-blue-600',
                'landing_status' => 'text-blue-600',
                'landing_hover_border' => 'hover:border-blue-400',
                'landing_hover_arrow' => 'group-hover:text-blue-500',
            ],
            'ready' => [
                'label' => 'Siap Disajikan',
                'icon' => 'bell',
                'gradient' => 'from-teal-500 to-emerald-600',
                'gradient_shadow' => 'shadow-teal-900/10',
                'card_border' => 'border-teal-200',
                'card_bg' => 'bg-teal-50',
                'icon_wrap_bg' => 'bg-teal-100',
                'icon_text' => 'text-teal-600',
                'step_bg' => 'bg-teal-500',
                'step_shadow' => 'shadow-teal-500/30',
                'connector' => 'bg-teal-400',
                'label_active' => 'text-teal-700',
                'desc_active' => 'text-teal-600',
                'landing_border' => 'border-teal-200',
                'landing_shadow' => 'shadow-teal-500/10',
                'landing_icon_bg' => 'bg-teal-500',
                'landing_icon_shadow' => 'shadow-teal-500/25',
                'landing_accent' => 'text-teal-600',
                'landing_status' => 'text-teal-600',
                'landing_hover_border' => 'hover:border-teal-400',
                'landing_hover_arrow' => 'group-hover:text-teal-500',
            ],
            'completed' => [
                'label' => 'Selesai',
                'icon' => 'sparkles',
                'gradient' => 'from-slate-600 to-slate-800',
                'gradient_shadow' => 'shadow-slate-900/10',
                'card_border' => 'border-slate-200',
                'card_bg' => 'bg-slate-50',
                'icon_wrap_bg' => 'bg-slate-100',
                'icon_text' => 'text-slate-600',
                'step_bg' => 'bg-slate-600',
                'step_shadow' => 'shadow-slate-500/30',
                'connector' => 'bg-slate-400',
                'label_active' => 'text-slate-700',
                'desc_active' => 'text-slate-600',
                'landing_border' => 'border-slate-200',
                'landing_shadow' => 'shadow-slate-500/10',
                'landing_icon_bg' => 'bg-slate-600',
                'landing_icon_shadow' => 'shadow-slate-500/25',
                'landing_accent' => 'text-slate-600',
                'landing_status' => 'text-slate-600',
                'landing_hover_border' => 'hover:border-slate-400',
                'landing_hover_arrow' => 'group-hover:text-slate-500',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'icon' => 'clock',
                'gradient' => 'from-red-500 to-red-600',
                'gradient_shadow' => 'shadow-red-900/10',
                'card_border' => 'border-red-200',
                'card_bg' => 'bg-red-50',
                'icon_wrap_bg' => 'bg-red-100',
                'icon_text' => 'text-red-600',
                'step_bg' => 'bg-red-500',
                'step_shadow' => 'shadow-red-500/30',
                'connector' => 'bg-red-400',
                'label_active' => 'text-red-700',
                'desc_active' => 'text-red-600',
                'landing_border' => 'border-red-200',
                'landing_shadow' => 'shadow-red-500/10',
                'landing_icon_bg' => 'bg-red-500',
                'landing_icon_shadow' => 'shadow-red-500/25',
                'landing_accent' => 'text-red-600',
                'landing_status' => 'text-red-600',
                'landing_hover_border' => 'hover:border-red-400',
                'landing_hover_arrow' => 'group-hover:text-red-500',
            ],
        ];
    }

    public function getStatusThemeAttribute(): array
    {
        return self::statusTheme($this->status);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public static function generateOrderNumber(): string
    {
        $today = now()->format('Ymd');
        $lastOrder = self::where('order_number', 'like', "ORD-{$today}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ORD-{$today}-{$newNumber}";
    }
}
