<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeuanganBukuBankLampiran extends Model
{
    protected $table = 'keuangan_buku_bank_lampiran';
    protected $fillable = ['buku_bank_id', 'transaksi_id', 'file_path'];

    public function bukuBank(): BelongsTo { return $this->belongsTo(KeuanganBukuBank::class, 'buku_bank_id'); }
    public function transaksi(): BelongsTo { return $this->belongsTo(KeuanganBukuBankTransaksi::class, 'transaksi_id'); }
}
