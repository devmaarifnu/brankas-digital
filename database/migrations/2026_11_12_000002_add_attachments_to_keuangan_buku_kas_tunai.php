<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keuangan_buku_kas_tunai', function (Blueprint $table) {
            $table->string('saldo_awal_lampiran')->nullable()->after('saldo_awal');
        });

        Schema::table('keuangan_buku_kas_tunai_transaksi', function (Blueprint $table) {
            $table->string('lampiran')->nullable()->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('keuangan_buku_kas_tunai_transaksi', function (Blueprint $table) {
            $table->dropColumn('lampiran');
        });

        Schema::table('keuangan_buku_kas_tunai', function (Blueprint $table) {
            $table->dropColumn('saldo_awal_lampiran');
        });
    }
};
