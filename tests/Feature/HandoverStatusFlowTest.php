<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SuratTanah;
use App\Models\AktaNotaris;
use App\Models\DataAsetLembaga;
use App\Models\RecordOfHandover;
use Tests\TestCase;

class HandoverStatusFlowTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin_handover'],
            ['name' => 'Admin Handover', 'email' => 'admin_handover@test.com', 'password' => bcrypt('password'), 'role' => 'super admin', 'status_active' => 'active']
        );
        $this->admin->role = 'super admin';
        $this->admin->status_active = 'active';
        $this->admin->save();
    }

    public function test_handover_borrow_and_prevent_double_borrow()
    {
        $surat = SuratTanah::create([
            'nama_dokumen' => 'Sertifikat Tanah Uji',
            'nama_sertifikat' => 'Sertifikat Tanah Uji',
            'nomor_sertifikat' => 'SHM-TEST-FLOW-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '500',
            'status_handover' => 'Tersedia',
            'warna_merah' => false,
        ]);

        // 1. Pinjam dokumen
        $resBorrow = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dipinjam',
            'nama_peminjam' => 'Budi Santoso',
            'no_telp_peminjam' => '08123456789',
            'tgl_serahterima' => '2026-09-16',
        ]);
        $resBorrow->assertSessionHas('success');

        $surat->refresh();
        $this->assertEquals('Dipinjam', $surat->status_handover);
        $this->assertTrue((bool)$surat->warna_merah);

        // Verifikasi record_of_handover mencatat user_id
        $lastHandover = RecordOfHandover::where('ref_id', $surat->id)->where('kategori', 'Arsip Surat Tanah')->latest()->first();
        $this->assertNotNull($lastHandover);
        $this->assertEquals($this->admin->id_user, $lastHandover->user_id);
        $this->assertEquals('Admin Handover', $lastHandover->nama_petugas);

        // 2. Coba pinjam lagi dokumen yang sama (seharusnya DITOLAK)
        $resDoubleBorrow = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dipinjam',
            'nama_peminjam' => 'Orang Lain',
            'tgl_serahterima' => '2026-09-16',
        ]);
        $resDoubleBorrow->assertSessionHas('error');

        // 3. Coba agunkan langsung tanpa proses pengembalian (seharusnya DITOLAK)
        $resDirectMortgage = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Diagunkan',
            'nama_bank' => 'Bank Mandiri',
            'tgl_serahterima' => '2026-09-16',
        ]);
        $resDirectMortgage->assertSessionHas('error');

        // 4. Proses Pengembalian (Dikembalikan)
        $resReturn = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dikembalikan',
            'nama_peminjam' => 'Budi Santoso (Mengembalikan)',
            'tgl_serahterima' => '2026-09-17',
        ]);
        $resReturn->assertSessionHas('success');

        $surat->refresh();
        $this->assertEquals('Tersedia', $surat->status_handover);
        $this->assertFalse((bool)$surat->warna_merah);

        // 5. Coba kembalikan lagi saat sudah berstatus Tersedia (seharusnya DITOLAK)
        $resInvalidReturn = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dikembalikan',
            'tgl_serahterima' => '2026-09-18',
        ]);
        $resInvalidReturn->assertSessionHas('error');

        // 6. Sekarang sudah Tersedia, maka bisa diagunkan dengan sah
        $resMortgage = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Diagunkan',
            'nama_bank' => 'Bank Mandiri',
            'jangka_agunan' => '3 Tahun',
            'penanggung_agunan' => 'Ahmad',
            'tgl_serahterima' => '2026-09-18',
        ]);
        $resMortgage->assertSessionHas('success');

        $surat->refresh();
        $this->assertEquals('Diagunkan', $surat->status_handover);
        $this->assertTrue((bool)$surat->warna_merah);

        // 7. Periksa riwayat handovers pada model
        $handovers = $surat->handovers;
        $this->assertCount(3, $handovers); // Dipinjam, Dikembalikan, Diagunkan

        // Bersihkan data
        RecordOfHandover::where('ref_id', $surat->id)->where('kategori', 'Arsip Surat Tanah')->delete();
        $surat->delete();
    }

    public function test_handover_items_ajax_filter()
    {
        $suratAvailable = SuratTanah::create([
            'nama_dokumen' => 'Surat Tersedia',
            'nama_sertifikat' => 'Surat Tersedia',
            'nomor_sertifikat' => 'SHM-AVAIL-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '100',
            'status_handover' => 'Tersedia',
            'warna_merah' => false,
        ]);

        $suratBorrowed = SuratTanah::create([
            'nama_dokumen' => 'Surat Terpinjam',
            'nama_sertifikat' => 'Surat Terpinjam',
            'nomor_sertifikat' => 'SHM-BORROW-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '100',
            'status_handover' => 'Dipinjam',
            'warna_merah' => true,
        ]);

        // Filter untuk Dipinjam -> SEMUA dokumen tampil (bukan hanya yg tersedia)
        // Karena status di form brangkas sudah disederhanakan: hanya Tersedia / Isi Sendiri
        // Track record peminjaman dicatat via Record of Transfer
        $resAvail = $this->actingAs($this->admin)->getJson(route('handover.items', [
            'kategori' => 'Arsip Surat Tanah',
            'status' => 'Dipinjam',
        ]));
        $resAvail->assertStatus(200);
        $dataAvail = $resAvail->json();
        $idsAvail = array_column($dataAvail, 'id');
        // Kedua item harus muncul — karena semua data bisa jadi track record peminjaman
        $this->assertContains($suratAvailable->id, $idsAvail);
        $this->assertContains($suratBorrowed->id, $idsAvail);

        // Filter untuk Dikembalikan -> hanya menampilkan dokumen yang sedang aktif dipinjam (warna_merah=true)
        $resReturn = $this->actingAs($this->admin)->getJson(route('handover.items', [
            'kategori' => 'Arsip Surat Tanah',
            'status' => 'Dikembalikan',
        ]));
        $resReturn->assertStatus(200);
        $dataReturn = $resReturn->json();
        $idsReturn = array_column($dataReturn, 'id');
        $this->assertContains($suratBorrowed->id, $idsReturn);
        $this->assertNotContains($suratAvailable->id, $idsReturn);

        // Bersihkan
        $suratAvailable->delete();
        $suratBorrowed->delete();
    }

    public function test_custom_status_isi_sendiri_filter_and_display()
    {
        // 1. Submit form dengan Isi Sendiri & keterangan_custom
        $res = $this->actingAs($this->admin)->post(route('brangkas.surat-tanah.store'), [
            'nama_sertifikat' => 'Tanah Kas Desa Custom',
            'nomor_sertifikat' => 'SHM-CUSTOM-TEST-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '500',
            'keterangan' => 'Isi Sendiri',
            'keterangan_custom' => 'Arsip di Notaris',
        ]);
        $res->assertSessionHas('success');

        $doc = SuratTanah::where('nomor_sertifikat', 'SHM-CUSTOM-TEST-01')->first();
        $this->assertNotNull($doc);
        $this->assertEquals('Arsip di Notaris', $doc->keterangan);
        $this->assertEquals('Arsip di Notaris', $doc->status_handover);

        // 2. Akses halaman index -> pastikan teks "Arsip di Notaris" tampil di tabel
        $resIndex = $this->actingAs($this->admin)->get(route('brangkas.surat-tanah'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Arsip di Notaris');

        // 3. Filter "Isi Sendiri" -> dokumen custom harus ada di hasil
        $resFilterIsiSendiri = $this->actingAs($this->admin)->get(route('brangkas.surat-tanah', [
            'status_handover' => 'Isi Sendiri',
        ]));
        $resFilterIsiSendiri->assertStatus(200);
        $resFilterIsiSendiri->assertSee('SHM-CUSTOM-TEST-01');

        // 4. Filter "Tersedia" -> dokumen custom TIDAK boleh ada di hasil
        $resFilterTersedia = $this->actingAs($this->admin)->get(route('brangkas.surat-tanah', [
            'status_handover' => 'Tersedia',
        ]));
        $resFilterTersedia->assertStatus(200);
        $resFilterTersedia->assertDontSee('SHM-CUSTOM-TEST-01');

        // Bersihkan
        $doc->delete();
    }

    public function test_handover_surat_kendaraan_flow()
    {
        $sk = \App\Models\SuratKendaraan::create([
            'jenis_surat' => 'BPKB',
            'nama_kendaraan' => 'Isuzu Elf Ambulans',
            'no_plat' => 'B 1926 HVR',
            'status_handover' => 'Tersedia',
            'warna_merah' => false,
        ]);

        // 1. Pinjam BPKB
        $resBorrow = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Kendaraan',
            'ref_id' => $sk->id,
            'nama_dokumen' => $sk->nama_kendaraan,
            'status' => 'Dipinjam',
            'nama_peminjam' => 'Drs. Ahmad',
            'no_telp_peminjam' => '081122334455',
            'tgl_serahterima' => '2026-09-25',
        ]);
        $resBorrow->assertSessionHas('success');

        $sk->refresh();
        $this->assertEquals('Dipinjam', $sk->status_handover);
        $this->assertTrue((bool)$sk->warna_merah);

        // 2. Filter AJAX getItemsByKategori untuk status Dikembalikan
        $resItems = $this->actingAs($this->admin)->getJson(route('handover.items', [
            'kategori' => 'Arsip Surat Kendaraan',
            'status' => 'Dikembalikan',
        ]));
        $resItems->assertStatus(200);
        $ids = array_column($resItems->json(), 'id');
        $this->assertContains($sk->id, $ids);

        // 3. Kembalikan
        $resReturn = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Kendaraan',
            'ref_id' => $sk->id,
            'nama_dokumen' => $sk->nama_kendaraan,
            'status' => 'Dikembalikan',
            'nama_peminjam' => 'Drs. Ahmad (Pengembalian)',
            'tgl_serahterima' => '2026-09-26',
        ]);
        $resReturn->assertSessionHas('success');

        $sk->refresh();
        $this->assertEquals('Tersedia', $sk->status_handover);
        $this->assertFalse((bool)$sk->warna_merah);

        // Clean up
        RecordOfHandover::where('ref_id', $sk->id)->where('kategori', 'Arsip Surat Kendaraan')->delete();
        $sk->delete();
    }

    public function test_handover_destroy_and_resync_status()
    {
        $surat = SuratTanah::create([
            'nama_dokumen' => 'Sertifikat Uji Hapus',
            'nama_sertifikat' => 'Sertifikat Uji Hapus',
            'nomor_sertifikat' => 'SHM-TEST-DEL-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '300',
            'status_handover' => 'Tersedia',
            'warna_merah' => false,
        ]);

        // 1. Pinjam dokumen
        $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dipinjam',
            'nama_peminjam' => 'Budi Santoso',
            'tgl_serahterima' => '2026-09-28',
        ]);

        $surat->refresh();
        $this->assertEquals('Dipinjam', $surat->status_handover);
        $this->assertTrue((bool)$surat->warna_merah);

        $handover = RecordOfHandover::where('ref_id', $surat->id)->where('kategori', 'Arsip Surat Tanah')->latest()->first();
        $this->assertNotNull($handover);

        // 2. Hapus handover record
        $resDelete = $this->actingAs($this->admin)->post(route('handover.destroy', $handover->id));
        $resDelete->assertRedirect(route('handover.index'));
        $resDelete->assertSessionHas('success');

        // 3. Verifikasi record terhapus dan status surat kembali Tersedia
        $this->assertDatabaseMissing('record_of_handover', ['id' => $handover->id]);
        $surat->refresh();
        $this->assertEquals('Tersedia', $surat->status_handover);
        $this->assertFalse((bool)$surat->warna_merah);

        // Clean up
        $surat->delete();
    }
}

