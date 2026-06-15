<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status untuk menambahkan 'pending_confirmation'
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending_confirmation', 'pending', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Kembalikan ke enum semula
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
