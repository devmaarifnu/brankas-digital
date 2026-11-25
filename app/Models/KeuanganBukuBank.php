<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeuanganBukuBank extends Model
{
    protected $table = 'keuangan_buku_bank';
    protected $fillable = ['bulan', 'tahun', 'judul', 'keterangan', 'saldo_awal', 'saldo_awal_sumber_id', 'ditutup_pada'];
    protected $casts = ['bulan' => 'integer', 'tahun' => 'integer', 'saldo_awal' => 'decimal:2', 'ditutup_pada' => 'datetime'];

    public function transaksi(): HasMany { return $this->hasMany(KeuanganBukuBankTransaksi::class, 'buku_bank_id'); }
    public function lampiranSaldoAwal(): HasMany { return $this->hasMany(KeuanganBukuBankLampiran::class, 'buku_bank_id')->whereNull('transaksi_id'); }
    public function saldoAwalSumber(): BelongsTo { return $this->belongsTo(self::class, 'saldo_awal_sumber_id'); }
}
