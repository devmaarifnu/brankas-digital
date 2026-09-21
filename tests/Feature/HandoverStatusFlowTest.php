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
            ['name' => 'Admin Handover', 'email' => 'admin_handover@test.com', 'password' => bcrypt('password'), 'role' => 'admin', 'status_active' => 'active']
        );
        $this->admin->role = 'admin';
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

        // Filter untuk Dipinjam -> hanya menampilkan dokumen yang Tersedia
        $resAvail = $this->actingAs($this->admin)->getJson(route('handover.items', [
            'kategori' => 'Arsip Surat Tanah',
            'status' => 'Dipinjam',
        ]));
        $resAvail->assertStatus(200);
        $dataAvail = $resAvail->json();
        $idsAvail = array_column($dataAvail, 'id');
        $this->assertContains($suratAvailable->id, $idsAvail);
        $this->assertNotContains($suratBorrowed->id, $idsAvail);

        // Filter untuk Dikembalikan -> hanya menampilkan dokumen yang sedang Dipinjam
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
}
