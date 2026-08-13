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
        Schema::table('vouchers', function (Blueprint $table) {
            // Menyuntikkan foreign key secara aman ke kolom yang sudah ada
            $table->foreign('penghuni_id')
                  ->references('id')
                  ->on('penghunis')
                  ->onDelete('set null');

            $table->foreign('tagihan_id')
                  ->references('id')
                  ->on('tagihans')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Aturan lepas/drop foreign key jika migration di-rollback
            $table->dropForeign(['penghuni_id']);
            $table->dropForeign(['tagihan_id']);
        });
    }
};