<?php

namespace Tests\Feature;

use App\Models\SuratTanah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SecureFileUploadTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('username', 'admin')->first();
    }

    public function test_uploaded_file_is_stored_in_storage_app_and_not_in_public(): void
    {
        $file = UploadedFile::fake()->create('rahasia.pdf', 150, 'application/pdf');

        $data = [
            'jenis_sertifikat' => 'SHM',
            'nomor_sertifikat' => 'SECURE-001',
            'luas'             => '1000',
            'nama_sertifikat'  => 'Sertifikat Rahasia Yayasan',
            'keterangan'       => 'Dokumen Asli Ada',
            'file_dokumen'     => $file,
        ];

        $response = $this->actingAs($this->user)->post('/brangkas/surat-tanah', $data);
        $response->assertRedirect('/brangkas/surat-tanah');

        $surat = SuratTanah::where('nomor_sertifikat', 'SECURE-001')->first();
        $this->assertNotNull($surat);

        // 1. MUST exist in storage_path('app/...')
        $this->assertTrue(File::exists(storage_path('app/' . $surat->file_dokumen)), 'File should exist in storage/app/uploads');

        // 2. MUST NOT exist in public_path('...')
        $this->assertFalse(File::exists(public_path($surat->file_dokumen)), 'File MUST NOT exist in public/uploads');

        // Cleanup
        if (File::exists(storage_path('app/' . $surat->file_dokumen))) {
            File::delete(storage_path('app/' . $surat->file_dokumen));
        }
    }

    public function test_guest_cannot_access_uploaded_file(): void
    {
        // Path dummy yang dituju
        $dummyPath = 'surat-tanah/dummy_test_file.pdf';
        $fullStoragePath = storage_path('app/uploads/' . $dummyPath);

        if (!File::exists(dirname($fullStoragePath))) {
            File::makeDirectory(dirname($fullStoragePath), 0755, true);
        }
        File::put($fullStoragePath, 'isi dokumen rahasia');

        // Akses tanpa login (Guest)
        $response = $this->get('/uploads/' . $dummyPath);

        // Wajib diarahkan ke halaman login
        $response->assertRedirect('/login');

        // Cleanup
        if (File::exists($fullStoragePath)) {
            File::delete($fullStoragePath);
        }
    }

    public function test_authenticated_user_can_access_uploaded_file_with_anti_indexing_headers(): void
    {
        $dummyPath = 'surat-tanah/dokumen_terproteksi.pdf';
        $fullStoragePath = storage_path('app/uploads/' . $dummyPath);

        if (!File::exists(dirname($fullStoragePath))) {
            File::makeDirectory(dirname($fullStoragePath), 0755, true);
        }
        File::put($fullStoragePath, 'konten sertifikat resmi');

        // Akses dengan login
        $response = $this->actingAs($this->user)->get('/uploads/' . $dummyPath);

        $response->assertOk();
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        $this->assertStringContainsString('no-cache', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', (string) $response->headers->get('Cache-Control'));

        // Cleanup
        if (File::exists($fullStoragePath)) {
            File::delete($fullStoragePath);
        }
    }

    public function test_directory_traversal_is_blocked(): void
    {
        $response = $this->actingAs($this->user)->get('/uploads/../../.env');
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));
    }
}
