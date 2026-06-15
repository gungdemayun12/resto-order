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
        Schema::create('table_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('restaurant_tables')->onDelete('cascade');
            $table->string('session_token')->unique();
            $table->string('qr_scan_token')->unique(); // Token unik setiap scan
            $table->enum('status', ['active', 'expired', 'closed'])->default('active');
            $table->timestamp('started_at');
            $table->timestamp('expires_at');
            $table->timestamp('closed_at')->nullable();
            $table->string('closed_by')->nullable(); // admin/system
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['table_id', 'status']);
            $table->index('session_token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_sessions');
    }
};
