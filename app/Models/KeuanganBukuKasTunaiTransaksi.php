<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeuanganBukuKasTunaiTransaksi extends Model
{
    protected $table = 'keuangan_buku_kas_tunai_transaksi';

    protected $fillable = [
        'buku_kas_tunai_id',
        'tanggal',
        'jenis_transaksi',
        'kode_akun',
        'transaksi',
        'jumlah',
        'terverifikasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'terverifikasi' => 'boolean',
    ];

    public function bukuKasTunai(): BelongsTo
    {
        return $this->belongsTo(KeuanganBukuKasTunai::class, 'buku_kas_tunai_id');
    }
}
