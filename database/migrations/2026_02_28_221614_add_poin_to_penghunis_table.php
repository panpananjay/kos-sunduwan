<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            // Tambahkan kantong poin, nilai awalnya 0
            $table->integer('poin')->default(0)->after('kamar_id');
        });
    }

    public function down(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->dropColumn('poin');
        });
    }
};
