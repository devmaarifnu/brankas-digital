<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeuanganBukuKasTunai extends Model
{
    protected $table = 'keuangan_buku_kas_tunai';

    protected $fillable = ['bulan', 'tahun', 'judul', 'keterangan', 'saldo_awal', 'ditutup_pada'];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'saldo_awal' => 'decimal:2',
        'ditutup_pada' => 'datetime',
    ];

    public function transaksi(): HasMany
    {
        return $this->hasMany(KeuanganBukuKasTunaiTransaksi::class, 'buku_kas_tunai_id');
    }

    public function lampiranSaldoAwal(): HasMany
    {
        return $this->hasMany(KeuanganBukuKasTunaiLampiran::class, 'buku_kas_tunai_id')->whereNull('transaksi_id');
    }
}
