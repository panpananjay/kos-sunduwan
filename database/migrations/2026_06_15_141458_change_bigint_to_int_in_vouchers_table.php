<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE vouchers MODIFY id INT UNSIGNED AUTO_INCREMENT');
        DB::statement('ALTER TABLE vouchers MODIFY penghuni_id INT UNSIGNED NULL');
        DB::statement('ALTER TABLE vouchers MODIFY tagihan_id INT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE vouchers MODIFY id BIGINT UNSIGNED AUTO_INCREMENT');
        DB::statement('ALTER TABLE vouchers MODIFY penghuni_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE vouchers MODIFY tagihan_id BIGINT UNSIGNED NULL');
    }
};