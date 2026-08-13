<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Kita ubah kolom status cuma jadi 'belum_bayar' dan 'lunas'
        // Pilihan kosong dan 'menunggu_verifikasi' otomatis ilang
        Schema::table('tagihans', function (Blueprint $blueprint) {
            DB::statement("ALTER TABLE tagihans MODIFY COLUMN status ENUM('belum_bayar', 'lunas') DEFAULT 'belum_bayar'");
        });
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $blueprint) {
            DB::statement("ALTER TABLE tagihans MODIFY COLUMN status ENUM('belum_bayar', 'menunggu_verifikasi', 'lunas') DEFAULT 'belum_bayar'");
        });
    }
};