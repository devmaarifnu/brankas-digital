<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_buku_kas_tunai', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['bulan', 'tahun']);
        });

        Schema::create('keuangan_buku_kas_tunai_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_kas_tunai_id')->constrained('keuangan_buku_kas_tunai')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis_transaksi', 20);
            $table->string('kode_akun', 100);
            $table->string('transaksi');
            $table->decimal('jumlah', 15, 2);
            $table->boolean('terverifikasi')->default(false);
            $table->timestamps();
            $table->index(['buku_kas_tunai_id', 'tanggal', 'id'], 'kas_tunai_periode_tanggal_idx');
            $table->index('kode_akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_buku_kas_tunai_transaksi');
        Schema::dropIfExists('keuangan_buku_kas_tunai');
    }
};
