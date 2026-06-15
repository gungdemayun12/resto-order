<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Order::query()
            ->where('payment_method', 'online')
            ->where('payment_status', 'paid')
            ->whereIn('status', ['payment_success', 'pending'])
            ->update(['status' => 'processing']);
    }

    public function down(): void
    {
        //
    }
};
