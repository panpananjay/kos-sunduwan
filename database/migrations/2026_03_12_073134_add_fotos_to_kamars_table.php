<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kamars', function (Blueprint $table) {
            $table->string('foto_utama')->nullable()->after('status');
            $table->string('foto_dapur')->nullable()->after('foto_utama');
            $table->string('foto_kamar_mandi')->nullable()->after('foto_dapur');
        });
    }

    public function down(): void
    {
        Schema::table('kamars', function (Blueprint $table) {
            $table->dropColumn(['foto_utama', 'foto_dapur', 'foto_kamar_mandi']);
        });
    }
};
