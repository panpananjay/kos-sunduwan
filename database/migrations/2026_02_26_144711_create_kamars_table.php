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
        Schema::create('kamars', function (Blueprint $table) {
            // Ubah id menjadi INT UNSIGNED agar cocok dengan kamar_id di tabel penghunis
            $table->integerIncrements('id'); 
            $table->string('nomor_kamar');
            $table->integer('harga');
            $table->enum('status', ['kosong', 'terisi'])->default('kosong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};