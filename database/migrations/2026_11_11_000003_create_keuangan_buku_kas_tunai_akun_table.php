<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_buku_kas_tunai_akun', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_transaksi', 20);
            $table->string('nama', 255)->unique();
            $table->timestamps();
            $table->index('jenis_transaksi');
        });

        $expenseAccounts = [
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
        ];

        $now = now();
        $accounts = array_map(fn ($name) => [
            'jenis_transaksi' => 'pengeluaran',
            'nama' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ], $expenseAccounts);
        array_unshift($accounts, [
            'jenis_transaksi' => 'pemasukan',
            'nama' => 'Pencairan Cek',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('keuangan_buku_kas_tunai_akun')->insert($accounts);
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_buku_kas_tunai_akun');
    }
};
