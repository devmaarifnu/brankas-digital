<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keuangan_buku_kas_tunai', function (Blueprint $table) {
            $table->foreignId('saldo_awal_sumber_id')
                ->nullable()
                ->after('saldo_awal')
                ->constrained('keuangan_buku_kas_tunai')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('keuangan_buku_kas_tunai', function (Blueprint $table) {
            $table->dropConstrainedForeignId('saldo_awal_sumber_id');
        });
    }
};
