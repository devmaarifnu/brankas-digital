<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeuanganBukuKasTunaiAkun extends Model
{
    protected $table = 'keuangan_buku_kas_tunai_akun';

    protected $fillable = ['jenis_transaksi', 'nama'];

    public function transaksi(): HasMany
    {
        return $this->hasMany(KeuanganBukuKasTunaiTransaksi::class, 'kode_akun', 'nama');
    }
}
