<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kita drop unique constraint pada session_token agar satu session bisa punya banyak order
            // Di MySQL/SQLite cara drop index berbeda-beda, cara teraman adalah modify kolom
            $table->string('session_token')->change(); // Ini biasanya akan drop index di Laravel
        });
        
        // Coba drop index secara eksplisit jika memungkinkan
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropUnique(['session_token']);
            });
        } catch (\Exception $e) {
            // Ignore jika index tidak ada atau tidak bisa di-drop via Blueprint
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unique('session_token');
        });
    }
};
