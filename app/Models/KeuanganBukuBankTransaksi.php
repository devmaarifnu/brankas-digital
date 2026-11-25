<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeuanganBukuBankTransaksi extends Model
{
    protected $table = 'keuangan_buku_bank_transaksi';
    protected $fillable = ['buku_bank_id', 'tanggal', 'jenis_transaksi', 'kode_akun', 'transaksi', 'jumlah', 'terverifikasi'];
    protected $casts = ['tanggal' => 'date', 'jumlah' => 'decimal:2', 'terverifikasi' => 'boolean'];

    public function bukuBank(): BelongsTo { return $this->belongsTo(KeuanganBukuBank::class, 'buku_bank_id'); }
    public function lampiranFiles(): HasMany { return $this->hasMany(KeuanganBukuBankLampiran::class, 'transaksi_id'); }
}
