<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class KeuanganBukuBankAkun extends KeuanganBukuKasTunaiAkun
{
    protected $table = 'keuangan_buku_bank_akun';

    public function transaksi(): HasMany
    {
        return $this->hasMany(KeuanganBukuBankTransaksi::class, 'kode_akun', 'nama');
    }
}
