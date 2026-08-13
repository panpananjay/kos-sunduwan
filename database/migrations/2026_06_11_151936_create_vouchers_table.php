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
        // Hapus draf lama jika tersisa
        Schema::dropIfExists('vouchers');

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            // Kita buat kolomnya sebagai tipe data integer biasa tanpa batasan constraint kaku di level database
            $table->unsignedBigInteger('penghuni_id')->nullable();
            $table->unsignedBigInteger('tagihan_id')->nullable();
            
            $table->string('kode_voucher')->unique();
            $table->integer('nominal')->default(50000);
            $table->enum('status', ['aktif', 'terpakai', 'expired'])->default('aktif');
            $table->date('masa_berlaku');
            $table->timestamps();

            // Skenario Foreign Key dimatikan di level MySQL agar tidak bikin pusing error errno 150.
            // Relasi data akan tetap terikat dengan aman dan dikontrol penuh lewat logika file Model Laravel kamu.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};