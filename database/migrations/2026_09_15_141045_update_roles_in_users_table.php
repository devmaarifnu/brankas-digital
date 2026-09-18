<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Ubah kolom role menjadi VARCHAR agar fleksibel untuk 4 role utama:
            // 'super admin', 'admin', 'viewer', 'aproval'
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(50) NOT NULL DEFAULT 'viewer'");
            // Mapping role lama jika ada
            DB::statement("UPDATE users SET role = 'super admin' WHERE role = 'super admin'");
            DB::statement("UPDATE users SET role = 'admin' WHERE role IN ('admin pusat', 'admin wilayah', 'admin cabang', 'operator')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('super admin', 'admin pusat', 'admin wilayah', 'admin cabang', 'operator') NOT NULL DEFAULT 'operator'");
    }
};
