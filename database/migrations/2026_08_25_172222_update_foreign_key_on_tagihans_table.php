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
        Schema::table('tagihans', function (Blueprint $table) {
            // Hapus foreign key lama (cascade)
            $table->dropForeign(['penghuni_id']);

            // Buat ulang dengan restrict, supaya penghuni yang masih
            // punya riwayat tagihan tidak bisa dihapus permanen
            $table->foreign('penghuni_id')
                  ->references('id')
                  ->on('penghunis')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropForeign(['penghuni_id']);

            $table->foreign('penghuni_id')
                  ->references('id')
                  ->on('penghunis')
                  ->onDelete('cascade');
        });
    }
};