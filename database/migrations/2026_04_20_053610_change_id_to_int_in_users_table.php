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
        // 1. Matikan pengecekan FK biar MySQL gak rewel
        Schema::disableForeignKeyConstraints();

        // 2. Lepas foreign key hanya JIKA dia memang ada
        // Kita lakukan untuk semua tabel yang bermasalah tadi
        foreach (['pengaduans', 'penghunis', 'tagihans'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Cek apakah foreign key-nya ada (format default laravel: nama_tabel_kolom_foreign)
                    $foreignKeyName = $tableName . '_user_id_foreign';
                    
                    // Gunakan try-catch atau DB raw untuk mengabaikan error jika key sudah hilang
                    try {
                        $table->dropForeign($foreignKeyName);
                    } catch (\Exception $e) {
                        // Biarkan saja jika sudah terhapus
                    }
                });
            }
        }

        // 3. Sekarang baru ganti tipe datanya satu-satu
        DB::statement('ALTER TABLE users MODIFY id INT UNSIGNED AUTO_INCREMENT');
        
        if (Schema::hasTable('pengaduans')) 
            DB::statement('ALTER TABLE pengaduans MODIFY user_id INT UNSIGNED');
        
        if (Schema::hasTable('penghunis')) 
            DB::statement('ALTER TABLE penghunis MODIFY user_id INT UNSIGNED');
            
        if (Schema::hasTable('tagihans')) {
            DB::statement('ALTER TABLE tagihans MODIFY id INT UNSIGNED AUTO_INCREMENT');
            DB::statement('ALTER TABLE tagihans MODIFY user_id INT UNSIGNED');
        }

        // 4. Pasang kembali foreign key-nya
        foreach (['pengaduans', 'penghunis', 'tagihans'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
            }
        }

        // 5. Nyalakan lagi pengecekannya
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('pengaduans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE users MODIFY id BIGINT UNSIGNED AUTO_INCREMENT');
        DB::statement('ALTER TABLE pengaduans MODIFY user_id BIGINT UNSIGNED');

        Schema::table('pengaduans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
