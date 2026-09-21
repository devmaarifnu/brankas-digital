<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SuratTanah;
use App\Models\AktaNotaris;
use App\Models\DataAsetLembaga;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BrangkasDigitalTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('username', 'admin')->first();
    }

    public function test_all_brangkas_routes_return_ok()
    {
        $routes = [
            '/handover',
            '/handover/get-items?kategori=Arsip Surat Tanah',
            '/brangkas/surat-tanah',
            '/brangkas/akta-notaris',
            '/brangkas/data-aset',
            '/keuangan/pengajuan',
            '/keuangan/rekening-koran',
            '/keuangan/buku-bank',
            '/keuangan/buku-kas-tunai',
            '/keuangan/buku-kas-umum',
            '/keuangan/rekap-bulanan',
            '/keuangan/rekap-tahunan',
            '/setting/users',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $this->assertEquals(200, $response->getStatusCode(), "Failed on route: {$route}");
        }
    }

    public function test_surat_tanah_validation_fails_if_fields_missing()
    {
        $response = $this->actingAs($this->user)->post('/brangkas/surat-tanah', [
            'nama_sertifikat' => 'Sertifikat Test Incomplete',
        ]);
        $response->assertSessionHasErrors(['jenis_sertifikat', 'nomor_sertifikat', 'luas']);
    }

    public function test_surat_tanah_crud_with_standardized_fields_and_pdf_document()
    {
        $file = UploadedFile::fake()->create('surat_tanah.pdf', 100, 'application/pdf');
        $createData = [
            'jenis_sertifikat' => 'SHM',
            'nomor_sertifikat' => 'SHM-8899/2026',
            'luas'             => '2500',
            'nama_sertifikat'  => 'Sertifikat Tanah Yayasan Al-Maarif',
            'desa_kelurahan'   => 'Pegangsaan',
            'kecamatan'        => 'Menteng',
            'kabupaten_kota'   => 'Jakarta Pusat',
            'provinsi'         => 'DKI Jakarta',
            'nama_petugas'     => 'Faisol',
            'tgl_input'        => '2026-09-09',
            'keterangan'       => 'Dokumen Asli Ada',
            'file_dokumen'     => $file,
        ];

        $response = $this->actingAs($this->user)->post('/brangkas/surat-tanah', $createData);
        $response->assertRedirect('/brangkas/surat-tanah');
        $response->assertSessionHas('success');

        $surat = SuratTanah::where('nomor_sertifikat', 'SHM-8899/2026')->first();
        $this->assertNotNull($surat);
        $this->assertEquals('2500', $surat->luas);
        $this->assertEquals('Dokumen Asli Ada', $surat->keterangan);
        $this->assertFalse($surat->warna_merah);
        $this->assertTrue(File::exists(storage_path('app/' . $surat->file_dokumen)));
        $this->assertFalse(File::exists(public_path($surat->file_dokumen)));

        // Update
        $updateData = [
            'jenis_sertifikat' => 'SHM',
            'nomor_sertifikat' => 'SHM-8899/2026',
            'luas'             => '2500',
            'nama_sertifikat'  => 'Sertifikat Tanah Yayasan Al-Maarif (Updated)',
            'desa_kelurahan'   => 'Pegangsaan',
            'kecamatan'        => 'Menteng',
            'kabupaten_kota'   => 'Jakarta Pusat',
            'provinsi'         => 'DKI Jakarta',
            'keterangan'       => 'Diagunkan',
        ];
        $updateResponse = $this->actingAs($this->user)->post("/brangkas/surat-tanah/{$surat->id}/update", $updateData);
        $updateResponse->assertRedirect('/brangkas/surat-tanah');

        $surat->refresh();
        $this->assertEquals('Diagunkan', $surat->status_handover);
        $this->assertTrue($surat->warna_merah);

        // Delete
        $filePath = storage_path('app/' . $surat->file_dokumen);
        $deleteResponse = $this->actingAs($this->user)->post("/brangkas/surat-tanah/{$surat->id}/delete");
        $deleteResponse->assertRedirect('/brangkas/surat-tanah');
        $this->assertDatabaseMissing('surat_tanah', ['id' => $surat->id]);
        $this->assertFalse(File::exists($filePath));
    }

    public function test_akta_notaris_crud_with_standardized_fields_and_pdf()
    {
        $file = UploadedFile::fake()->create('akta_notaris.pdf', 100, 'application/pdf');
        $createData = [
            'jenis_dokumen'  => 'Akta Notaris',
            'nomor_dokumen'  => '99/AN/2026',
            'nama_dokumen'   => 'Akta Pendirian Cabang Surabaya',
            'tgl_dokumen'    => '2026-01-15',
            'nama_notaris'   => 'Hj. Endang, S.H.',
            'alamat_notaris' => 'Jl. Raya Darmo No. 12, Surabaya',
            'telp_notaris'   => '08123456789',
            'nama_petugas'   => 'Faisol',
            'tgl_input'      => '2026-09-09',
            'keterangan'     => 'Dokumen Asli Ada',
            'file_dokumen'   => $file,
        ];

        $response = $this->actingAs($this->user)->post('/brangkas/akta-notaris', $createData);
        $response->assertRedirect('/brangkas/akta-notaris');

        $akta = AktaNotaris::where('nomor_dokumen', '99/AN/2026')->first();
        $this->assertNotNull($akta);
        $this->assertEquals('Akta Notaris', $akta->jenis_dokumen);
        $this->assertTrue(File::exists(storage_path('app/' . $akta->file_dokumen)));
        $this->assertFalse(File::exists(public_path($akta->file_dokumen)));

        // Delete
        $filePath = storage_path('app/' . $akta->file_dokumen);
        $this->actingAs($this->user)->post("/brangkas/akta-notaris/{$akta->id}/delete");
        $this->assertDatabaseMissing('akta_notaris', ['id' => $akta->id]);
        $this->assertFalse(File::exists($filePath));
    }

    public function test_data_aset_crud_with_auto_registration_and_pdf()
    {
        $file = UploadedFile::fake()->create('foto_aset.pdf', 100, 'application/pdf');
        $createData = [
            'jenis_barang'      => 'Laptop',
            'nama_barang'       => 'ThinkPad T14 Gen 4',
            'merek'             => 'Lenovo',
            'nomor_seri_model'  => 'SN-TP14-9988',
            'sumber_perolehan'  => 'Beli',
            'tgl_perolehan'     => '2024-01-10',
            'kondisi_aset'      => 'Sangat Baik',
            'posisi_aset'       => 'Kantor',
            'nama_ruangan'      => 'Ruang Sekretariat Lt. 2',
            'nama_petugas'      => 'Faisol',
            'tgl_input'         => '2026-09-09',
            'keterangan'        => 'Dipakai kebutuhan operasional kantor',
            'file_dokumen'      => $file,
        ];

        $response = $this->actingAs($this->user)->post('/brangkas/data-aset', $createData);
        $response->assertRedirect('/brangkas/data-aset');

        $aset = DataAsetLembaga::where('nama_barang', 'ThinkPad T14 Gen 4')->first();
        $this->assertNotNull($aset);
        $this->assertStringStartsWith('AST-LPM-', $aset->nomor_registrasi);
        $this->assertEquals('Ruang Sekretariat Lt. 2', $aset->lokasi);
        $this->assertTrue(File::exists(storage_path('app/' . $aset->file_dokumen)));
        $this->assertFalse(File::exists(public_path($aset->file_dokumen)));

        // Delete
        $filePath = storage_path('app/' . $aset->file_dokumen);
        $this->actingAs($this->user)->post("/brangkas/data-aset/{$aset->id}/delete");
        $this->assertDatabaseMissing('data_aset_lembaga', ['id' => $aset->id]);
        $this->assertFalse(File::exists($filePath));
    }

    public function test_handover_submission_and_auto_red_flag()
    {
        $surat = SuratTanah::firstOrCreate(
            ['nama_sertifikat' => 'Sertifikat Wakaf Handover Test'],
            ['nomor_sertifikat' => 'WKF-001', 'luas' => '1000', 'jenis_sertifikat' => 'Wakaf']
        );

        $payload = [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dipinjam',
            'nama_peminjam' => 'Budi Santoso',
            'no_telp_peminjam' => '08123456789',
            'tgl_serahterima' => '2026-09-09',
            'catatan' => 'Peminjaman untuk keperluan verifikasi akreditasi',
        ];

        $response = $this->actingAs($this->user)->post('/handover', $payload);
        $response->assertSessionHas('success');

        $surat->refresh();
        $this->assertEquals('Dipinjam', $surat->status_handover);
        $this->assertTrue($surat->warna_merah);
    }
}