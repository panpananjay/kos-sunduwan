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
       Schema::table('penghunis', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('kamar_id')->references('id')->on('kamars')->onDelete('cascade');
    });

    Schema::table('pengaduans', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });

    Schema::table('tagihans', function (Blueprint $table) {
        // Sesuaikan nama kolom tadi: penghuni_id
        $table->foreign('penghuni_id')->references('id')->on('penghunis')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tables', function (Blueprint $table) {
            //
        });
    }
};
