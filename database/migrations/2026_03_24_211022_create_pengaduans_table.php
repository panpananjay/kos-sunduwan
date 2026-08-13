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
            $table->integerIncrements('id'); // INT UNSIGNED AUTO_INCREMENT

            // 1. Ubah tipe kolom user_id menjadi unsignedInteger agar cocok dengan users.id
            $table->unsignedInteger('user_id');

            $table->text('isi_pengaduan');
            $table->enum('status', ['pending', 'proses', 'selesai'])->default('pending');
            $table->timestamps();

            // 2. Definisi Foreign Key
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
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