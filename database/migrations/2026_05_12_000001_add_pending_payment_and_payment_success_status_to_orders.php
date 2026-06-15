<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending_payment', 'payment_success', 'pending_confirmation', 'pending', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending_confirmation', 'pending', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
