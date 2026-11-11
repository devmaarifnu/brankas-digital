<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeuanganBukuKasTunai extends Model
{
    protected $table = 'keuangan_buku_kas_tunai';

    protected $fillable = ['bulan', 'tahun', 'judul', 'keterangan', 'saldo_awal'];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'saldo_awal' => 'decimal:2',
    ];

    public function transaksi(): HasMany
    {
        return $this->hasMany(KeuanganBukuKasTunaiTransaksi::class, 'buku_kas_tunai_id');
    }
}
