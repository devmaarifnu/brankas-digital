<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keuangan_buku_kas_tunai', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('tahun');
            $table->text('keterangan')->nullable()->after('judul');
        });
    }

    public function down(): void
    {
        Schema::table('keuangan_buku_kas_tunai', function (Blueprint $table) {
            $table->dropColumn(['judul', 'keterangan']);
        });
    }
};
