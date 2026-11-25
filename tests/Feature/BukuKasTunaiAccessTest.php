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

    public function test_admin_can_create_report_without_manual_opening_balance(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.store'), [
            'bulan' => 12,
            'tahun' => 2026,
        ])->assertRedirect(route('keuangan.buku-kas-tunai'));

        $report = KeuanganBukuKasTunai::where('bulan', 12)->where('tahun', 2026)->firstOrFail();
        $this->assertSame('0.00', $report->saldo_awal);
        $this->assertNull($report->saldo_awal_sumber_id);
    }

    public function test_admin_can_link_opening_balance_to_previous_month_closing_balance(): void
    {
        $admin = $this->userWithRole('admin');
        $previous = KeuanganBukuKasTunai::create([
            'bulan' => 10,
            'tahun' => 2026,
            'saldo_awal' => 150000,
        ]);
        $incoming = KeuanganBukuKasTunaiAkun::where('jenis_transaksi', 'pemasukan')->firstOrFail();
        $outgoing = KeuanganBukuKasTunaiAkun::where('jenis_transaksi', 'pengeluaran')->firstOrFail();
        $previous->transaksi()->create([
            'tanggal' => '2026-10-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan Oktober',
            'jumlah' => 50000,
        ]);
        $previous->transaksi()->create([
            'tanggal' => '2026-10-03',
            'jenis_transaksi' => 'pengeluaran',
            'kode_akun' => $outgoing->nama,
            'transaksi' => 'Pengeluaran Oktober',
            'jumlah' => 25000,
        ]);
        $current = $this->createReport();

        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.saldo-awal.ambil', $current))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('175000.00', $current->fresh()->saldo_awal);
        $this->assertSame($previous->id, $current->fresh()->saldo_awal_sumber_id);

        $this->actingAs($admin)->get(route('keuangan.buku-kas-tunai.show', $current))
            ->assertOk()
            ->assertSee('Diambil dari Oktober 2026')
            ->assertSeeText('Ambil & tautkan')
            ->assertSeeText('saldo akhir Rp 175.000');
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

    public function test_admin_can_edit_only_unverified_transactions_while_book_is_open(): void
    {
        $admin = $this->userWithRole('admin');
        $superAdmin = $this->userWithRole('super admin');
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

        $detailResponse = $this->actingAs($admin)->get(route('keuangan.buku-kas-tunai.show', $report))->assertOk();
        $navigation = new \DOMDocument();
        @$navigation->loadHTML($detailResponse->getContent());
        $navigationXPath = new \DOMXPath($navigation);
        $cashbookLink = $navigationXPath->query('//a[contains(@href, "/keuangan/buku-kas-tunai")]')->item(0);
        $this->assertNotNull($cashbookLink);
        $this->assertStringContainsString('active', $cashbookLink->getAttribute('class'));
        $detailResponse->assertSee('title="Edit transaksi"', false)
            ->assertDontSee('title="Hapus transaksi"', false);

        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.update', [$report, $transaction]), [
            'tanggal' => '2026-11-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Diperbarui Admin',
            'jumlah' => 60000,
        ])->assertRedirect();
        $this->assertSame('60000.00', $transaction->fresh()->jumlah);
        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.destroy', [$report, $transaction]))
            ->assertForbidden();

        $this->actingAs($superAdmin)->post(route('keuangan.buku-kas-tunai.transaksi.update', [$report, $transaction]), [
            'tanggal' => '2026-11-02',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan diperbarui',
            'jumlah' => 70000,
        ])->assertRedirect();
        $this->assertSame('70000.00', $transaction->fresh()->jumlah);

        $this->actingAs($superAdmin)->post(route('keuangan.buku-kas-tunai.transaksi.destroy', [$report, $transaction]))
            ->assertRedirect();
        $this->assertDatabaseMissing('keuangan_buku_kas_tunai_transaksi', ['id' => $transaction->id]);

        $verifiedTransaction = $report->transaksi()->create([
            'tanggal' => '2026-11-03',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan akan diverifikasi',
            'jumlah' => 20000,
        ]);
        $this->actingAs($approval)->post(route('keuangan.buku-kas-tunai.transaksi.verify', [$report, $verifiedTransaction]))
            ->assertRedirect();
        $this->assertTrue($verifiedTransaction->fresh()->terverifikasi);

        $this->actingAs($admin)->get(route('keuangan.buku-kas-tunai.show', $report))
            ->assertOk()
            ->assertDontSee('title="Edit transaksi"', false);
        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.update', [$report, $verifiedTransaction]), [
            'tanggal' => '2026-11-03',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Tidak boleh diedit',
            'jumlah' => 25000,
        ])->assertForbidden();

        $closedBookTransaction = $report->transaksi()->create([
            'tanggal' => '2026-11-04',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Penerimaan pada buku terbuka',
            'jumlah' => 10000,
        ]);
        $this->actingAs($approval)->post(route('keuangan.buku-kas-tunai.status', $report))->assertRedirect();
        $this->actingAs($admin)->get(route('keuangan.buku-kas-tunai.show', $report))
            ->assertOk()
            ->assertDontSee('title="Edit transaksi"', false);
        $this->actingAs($admin)->post(route('keuangan.buku-kas-tunai.transaksi.update', [$report, $closedBookTransaction]), [
            'tanggal' => '2026-11-04',
            'jenis_transaksi' => 'pemasukan',
            'kode_akun' => $incoming->nama,
            'transaksi' => 'Tidak boleh diedit setelah buku ditutup',
            'jumlah' => 15000,
        ])->assertForbidden();
    }
}
