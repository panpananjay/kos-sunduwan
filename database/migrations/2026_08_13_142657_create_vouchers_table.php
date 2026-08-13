<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penghuni_id')->constrained('penghunis')->onDelete('cascade');
            $table->foreignId('tagihan_id')->nullable()->constrained('tagihans')->onDelete('set null');
            $table->string('kode_voucher')->unique();
            $table->decimal('nominal', 12, 2);
            $table->enum('status', ['aktif', 'terpakai', 'expired'])->default('aktif');
            $table->date('masa_berlaku');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};