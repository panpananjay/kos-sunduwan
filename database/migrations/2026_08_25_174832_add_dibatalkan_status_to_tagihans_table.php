<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE tagihans MODIFY COLUMN status ENUM('belum_bayar', 'lunas', 'dibatalkan') NOT NULL DEFAULT 'belum_bayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Catatan: kalau ada row dengan status 'dibatalkan' saat rollback,
        // ALTER ini akan gagal karena value-nya nggak valid lagi di enum lama.
        // Pastikan tidak ada row 'dibatalkan' sebelum rollback, atau update dulu manual.
        DB::statement("ALTER TABLE tagihans MODIFY COLUMN status ENUM('belum_bayar', 'lunas') NOT NULL DEFAULT 'belum_bayar'");
    }
};