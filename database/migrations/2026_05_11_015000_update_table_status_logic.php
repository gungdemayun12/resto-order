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
        Schema::table('restaurant_tables', function (Blueprint $table) {
            // Kita gunakan raw change karena enum di Laravel migrasi agak ribet kalau di-change
            // Tapi kita bisa tambahkan status baru dengan memodifikasi kolom
            // Untuk amannya di SQLite/MySQL, kita biarkan status yang ada dan tambahkan logic di model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
