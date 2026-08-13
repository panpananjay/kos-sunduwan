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
           // Menambahkan foreign key yang berelasi ke tabel penagihan/penghuni
           $table->foreignId('penghuni_id')->nullable()->constrained('penghunis')->onDelete('cascade');
       });
   }

   public function down(): void
   {
       Schema::table('vouchers', function (Blueprint $table) {
           $table->dropForeign(['penghuni_id']);
           $table->dropColumn('penghuni_id');
       });
   }
};