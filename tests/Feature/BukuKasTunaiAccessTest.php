<?php

namespace Tests\Feature;

use App\Models\KeuanganBukuKasTunai;
use App\Models\KeuanganBukuKasTunaiAkun;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BukuKasTunaiAccessTest extends TestCase
{
    private function userWithRole(string $role): User
    {
        return User::create([
            'name' => ucfirst($role).' Test',
            'email' => str_replace(' ', '-', $role).'-'.uniqid().'@test.local',
            'username' => str_replace(' ', '-', $role).'-'.uniqid(),
            'password' => bcrypt('password'),
            'role' => $role,
            'status_active' => 'active',
        ]);
    }

    private function createReport(): KeuanganBukuKasTunai
    {
        return KeuanganBukuKasTunai::create([
            'bulan' => 11,
            'tahun' => 2026,
            'saldo_awal' => 150000,
        ]);
    }

    public function test_admin_can_create_report_transaction_and_multiple_private_attachments(): void
    {
        Storage::fake('uploads');
        $admin = $this->userWithRole('admin');
        $incoming = KeuanganBukuKasTunaiAkun::where('jenis_transaksi', 'pemasukan')->firstOrFail();

        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.store'), [
            'bulan' => 11,
            'tahun' => 2026,
            'judul' => 'Uji lokal',
            'saldo_awal' => 150000,
            'saldo_awal_lampiran' => [
                UploadedFile::fake()->create('bukti-1.pdf', 10, 'application/pdf'),
                UploadedFile::fake()->create('bukti-2.pdf', 10, 'application/pdf'),
            ],
        ])->assertRedirect(route('keuangan.buku-kas-tunai'));

        $report = KeuanganBukuKasTunai::where('bulan', 11)->where('tahun', 2026)->firstOrFail();
        $this->assertCount(2, $report->lampiranSaldoAwal);
        foreach ($report->lampiranSaldoAwal as $attachment) {
            $this->assertStringStartsWith('uploads/buku-kas-tunai/', $attachment->file_path);
            Storage::disk('uploads')->assertExists(str_replace('uploads/', '', $attachment->file_path));
        }

        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.store', $report), [
            'tanggal' => '2026-11-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan uji lokal',
            'jumlah' => 50000,
            'lampiran' => [UploadedFile::fake()->create('bukti-transaksi.pdf', 10, 'application/pdf')],
        ])->assertRedirect();

        $transaction = $report->transaksi()->firstOrFail();
        $this->assertCount(1, $transaction->lampiranFiles);
        Storage::disk('uploads')->assertExists(str_replace('uploads/', '', $transaction->lampiranFiles[0]->file_path));

        $this->actingAs($admin)->get(route('keuangan.buku-kas-tunai.show', $report))
            ->assertOk()
            ->assertSee('Sisa Saldo Bulan Sebelumnya')
            ->assertSee('Penerimaan uji lokal')
            ->assertSee('Tambah Transaksi');

        $export = $this->actingAs($admin)->get(route('keuangan.buku-kas-tunai.export', $report));
        $export->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $export->headers->get('content-type'));
    }

    public function test_approval_can_close_and_reopen_but_cannot_add_or_delete_transactions(): void
    {
        $approval = $this->userWithRole('aproval');
        $report = $this->createReport();
        $incoming = KeuanganBukuKasTunaiAkun::where('jenis_transaksi', 'pemasukan')->firstOrFail();
        $transaction = $report->transaksi()->create([
            'tanggal' => '2026-11-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan awal',
            'jumlah' => 50000,
        ]);

        $this->actingAs($approval)->get(route('keuangan.buku-kas-tunai.show', $report))
            ->assertOk()
            ->assertSee('Tutup Buku')
            ->assertDontSee('Tambah Transaksi');

        $this->actingAs($approval)->post(route('keuangan.buku-kas-tunai.transaksi.store', $report), [
            'tanggal' => '2026-11-03',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Tidak boleh ditambahkan',
            'jumlah' => 1000,
        ])->assertForbidden();

        $this->actingAs($approval)->post(route('keuangan.buku-kas-tunai.transaksi.destroy', [$report, $transaction]))
            ->assertForbidden();

        $this->actingAs($approval)->post(route('keuangan.buku-kas-tunai.transaksi.verify', [$report, $transaction]))
            ->assertRedirect();
        $this->assertTrue($transaction->fresh()->terverifikasi);

        $this->actingAs($approval)->post(route('keuangan.buku-kas-tunai.status', $report))->assertRedirect();
        $this->assertNotNull($report->fresh()->ditutup_pada);
        $admin = $this->userWithRole('admin');
        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.store', $report), [
            'tanggal' => '2026-11-03',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Tidak bisa saat buku ditutup',
            'jumlah' => 1000,
        ])->assertForbidden();

        $this->actingAs($approval)->post(route('keuangan.buku-kas-tunai.status', $report))->assertRedirect();
        $this->assertNull($report->fresh()->ditutup_pada);
    }

    public function test_regular_admin_cannot_edit_or_delete_but_super_admin_can_manage_unverified_transaction(): void
    {
        $admin = $this->userWithRole('admin');
        $superAdmin = $this->userWithRole('super admin');
        $report = $this->createReport();
        $incoming = KeuanganBukuKasTunaiAkun::where('jenis_transaksi', 'pemasukan')->firstOrFail();
        $transaction = $report->transaksi()->create([
            'tanggal' => '2026-11-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan awal',
            'jumlah' => 50000,
        ]);

        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.destroy', [$report, $transaction]))
            ->assertForbidden();
        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.update', [$report, $transaction]), [
            'tanggal' => '2026-11-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Ubah tanpa izin',
            'jumlah' => 60000,
        ])->assertForbidden();

        $this->actingAs($superAdmin)->post(route('keuangan.buku-kas-tunai.transaksi.update', [$report, $transaction]), [
            'tanggal' => '2026-11-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan diperbarui',
            'jumlah' => 60000,
        ])->assertRedirect();
        $this->assertSame('60000.00', $transaction->fresh()->jumlah);

        $this->actingAs($superAdmin)->post(route('keuangan.buku-kas-tunai.transaksi.destroy', [$report, $transaction]))
            ->assertRedirect();
        $this->assertDatabaseMissing('keuangan_buku_kas_tunai_transaksi', ['id' => $transaction->id]);
    }
}
