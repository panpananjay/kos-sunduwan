<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        // 1. Tambah kolom 'role' di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('penghuni')->after('username'); 
        });

        // 2. Tambah kolom 'user_id' di tabel penghunis (untuk menyambungkan akun login)
        Schema::table('penghunis', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
