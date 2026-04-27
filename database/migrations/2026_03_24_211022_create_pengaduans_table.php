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
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            // Relasi ke User (Penghuni yang melapor)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Isi Laporan
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('foto')->nullable(); // Opsional kalau mau lampirkan foto
            
            // Status & Respon
            $table->enum('status', ['pending', 'diproses', 'selesai'])->default('pending');
            $table->text('tanggapan_admin')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
