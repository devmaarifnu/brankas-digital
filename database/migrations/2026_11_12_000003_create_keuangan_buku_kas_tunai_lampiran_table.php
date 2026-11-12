<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_buku_kas_tunai_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_kas_tunai_id')->constrained('keuangan_buku_kas_tunai')->cascadeOnDelete();
            $table->foreignId('transaksi_id')->nullable()->constrained('keuangan_buku_kas_tunai_transaksi')->cascadeOnDelete();
            $table->string('file_path');
            $table->timestamps();
            $table->index(['buku_kas_tunai_id', 'transaksi_id'], 'kas_tunai_lampiran_parent_idx');
        });

        DB::table('keuangan_buku_kas_tunai')
            ->whereNotNull('saldo_awal_lampiran')
            ->where('saldo_awal_lampiran', '<>', '')
            ->get(['id', 'saldo_awal_lampiran'])
            ->each(fn ($report) => DB::table('keuangan_buku_kas_tunai_lampiran')->insert([
                'buku_kas_tunai_id' => $report->id,
                'transaksi_id' => null,
                'file_path' => $report->saldo_awal_lampiran,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

        DB::table('keuangan_buku_kas_tunai_transaksi')
            ->whereNotNull('lampiran')
            ->where('lampiran', '<>', '')
            ->get(['id', 'buku_kas_tunai_id', 'lampiran'])
            ->each(fn ($transaction) => DB::table('keuangan_buku_kas_tunai_lampiran')->insert([
                'buku_kas_tunai_id' => $transaction->buku_kas_tunai_id,
                'transaksi_id' => $transaction->id,
                'file_path' => $transaction->lampiran,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_buku_kas_tunai_lampiran');
    }
};
