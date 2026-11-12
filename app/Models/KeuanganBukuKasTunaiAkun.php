<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeuanganBukuKasTunaiAkun extends Model
{
    private const DISPLAY_ORDER = [
        '1.1. Honorarium Staff',
        '1.2. THR',
        '1.3. Belanja Kebutuhan Rumah Tangga (Dapur dan Alat Kebersihan)',
        '1.4. Konsumsi Harian dan Rapat',
        '1.5. Transport Rapat',
        '1.6. Trasnport Lokal Operasional Kantor',
        '1.7. Tagihan Telepon, Internet, Website, Zoom Meeting dan Privy',
        '1.8. ATK, Foto copy, Pos/Jasa Pengantaran',
        '1.9. Cetak Buku, Kalender, Proposal dan Laporan',
        '1.10. Pembayaran Beban Pajak',
        '1.11. Pembelian Peralatan Penunjang Kantor',
        '1.12. Perawatan Peralatan Penunjang Kantor',
        '1.13. Perawatan dan Perbaikan Kendaraan/Servis Berkala',
        '1.14. Perawatan dan Perbaikan Gedung',
        '1.15. Tagihan Listrik',
        '1.16. Pengelolaan Website, Data dan Media Publikasi',
        '1.17. Pembayaran Biaya Administrasi',
        '1.18. Iuran Kebersihan',
        '1.19. Bantuan/Santunan/Karangan Bunga',
        '1.20. Operasional Distribusi Badan Hukum',
        '1.21. Pembayaran Biaya Jasa',
        '2.OPERASIONAL PERJALANAN DAERAH.',
        '3.BIAYA PROGRAM/KEGIATAN.',
        'Pencairan Cek',
    ];

    protected $table = 'keuangan_buku_kas_tunai_akun';

    protected $fillable = ['jenis_transaksi', 'nama'];

    public static function sortForDisplay($accounts)
    {
        $priorities = array_flip(self::DISPLAY_ORDER);

        return $accounts->sort(function ($left, $right) use ($priorities) {
            $leftPriority = $priorities[$left->nama] ?? PHP_INT_MAX;
            $rightPriority = $priorities[$right->nama] ?? PHP_INT_MAX;

            return ($leftPriority <=> $rightPriority)
                ?: strcasecmp($left->nama, $right->nama);
        })->values();
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(KeuanganBukuKasTunaiTransaksi::class, 'kode_akun', 'nama');
    }
}
