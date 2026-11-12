<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeuanganBukuKasTunaiLampiran extends Model
{
    protected $table = 'keuangan_buku_kas_tunai_lampiran';

    protected $fillable = ['buku_kas_tunai_id', 'transaksi_id', 'file_path'];

    public function bukuKasTunai(): BelongsTo
    {
        return $this->belongsTo(KeuanganBukuKasTunai::class, 'buku_kas_tunai_id');
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(KeuanganBukuKasTunaiTransaksi::class, 'transaksi_id');
    }
}
