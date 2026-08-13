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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->integerIncrements('id'); // INT UNSIGNED AUTO_INCREMENT
            
            // Relasi ke tabel penghuni (Tipe INT UNSIGNED agar cocok dengan penghunis.id)
            $table->unsignedInteger('penghuni_id');
            
            // Informasi Bulan dan Tahun Tagihan
            $table->string('bulan'); // Contoh: "Maret"
            $table->string('tahun'); // Contoh: "2026"
            
            // Jumlah uang yang harus dibayar
            $table->integer('jumlah_tagihan');
            
            // Status pembayaran (Default-nya belum bayar)
            $table->enum('status', ['belum_bayar', 'menunggu_verifikasi', 'lunas'])->default('belum_bayar');
            
            // Tempat menyimpan nama file foto bukti transfer
            $table->string('bukti_bayar')->nullable(); 
            
            $table->timestamps();

            // Deklarasi Foreign Key eksplisit
            $table->foreign('penghuni_id')
                  ->references('id')
                  ->on('penghunis')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};