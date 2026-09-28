<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SuratTanah;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    protected $superAdmin;
    protected $admin;
    protected $viewer;
    protected $aproval;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['username' => 'test_superadmin'],
            ['name' => 'Test Super Admin', 'email' => 'superadmin@test.com', 'password' => bcrypt('password'), 'role' => 'super admin', 'status_active' => 'active']
        );
        $this->superAdmin->role = 'super admin';
        $this->superAdmin->status_active = 'active';
        $this->superAdmin->save();

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin'],
            ['name' => 'Test Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'role' => 'admin', 'status_active' => 'active']
        );
        $this->admin->role = 'admin';
        $this->admin->status_active = 'active';
        $this->admin->save();

        $this->viewer = User::firstOrCreate(
            ['username' => 'test_viewer'],
            ['name' => 'Test Viewer', 'email' => 'viewer@test.com', 'password' => bcrypt('password'), 'role' => 'viewer', 'status_active' => 'active']
        );
        $this->viewer->role = 'viewer';
        $this->viewer->status_active = 'active';
        $this->viewer->save();

        $this->aproval = User::firstOrCreate(
            ['username' => 'test_aproval'],
            ['name' => 'Test Aproval', 'email' => 'aproval@test.com', 'password' => bcrypt('password'), 'role' => 'aproval', 'status_active' => 'active']
        );
        $this->aproval->role = 'aproval';
        $this->aproval->status_active = 'active';
        $this->aproval->save();
    }

    public function test_super_admin_has_full_access_and_can_delete()
    {
        $surat = SuratTanah::create([
            'nama_dokumen' => 'Surat Test Super Admin',
            'nama_sertifikat' => 'Surat Test Super Admin',
            'nomor_sertifikat' => 'SHM-SUPER-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '100',
        ]);

        $res = $this->actingAs($this->superAdmin)->get(route('setting.users'));
        $res->assertStatus(200);

        $del = $this->actingAs($this->superAdmin)->post(route('brangkas.surat-tanah.destroy', $surat->id));
        $del->assertRedirect(route('brangkas.surat-tanah'));
        $del->assertSessionHas('success');
        $this->assertNull(SuratTanah::find($surat->id));
    }

    public function test_admin_can_input_and_handover_but_cannot_delete()
    {
        $surat = SuratTanah::create([
            'nama_dokumen' => 'Surat Test Admin',
            'nama_sertifikat' => 'Surat Test Admin',
            'nomor_sertifikat' => 'SHM-ADMIN-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '200',
        ]);

        $postRes = $this->actingAs($this->admin)->post(route('brangkas.surat-tanah.store'), [
            'nama_sertifikat' => 'Surat Baru Admin',
            'nomor_sertifikat' => 'SHM-ADMIN-02',
            'jenis_sertifikat' => 'SHM',
            'luas' => '350',
        ]);
        $postRes->assertRedirect(route('brangkas.surat-tanah'));
        $postRes->assertSessionHas('success');

        $hoRes = $this->actingAs($this->admin)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dipinjam',
            'tgl_serahterima' => '2026-09-15',
        ]);
        $hoRes->assertRedirect();
        $hoRes->assertSessionHas('success');

        $delRes = $this->actingAs($this->admin)->post(route('brangkas.surat-tanah.destroy', $surat->id));
        $delRes->assertSessionHas('error');
        $this->assertNotNull(SuratTanah::find($surat->id));

        $setRes = $this->actingAs($this->admin)->get(route('setting.users'));
        $setRes->assertRedirect(route('handover.index'));
        $setRes->assertSessionHas('error');

        $surat->delete();
        SuratTanah::where('nomor_sertifikat', 'SHM-ADMIN-02')->delete();
    }

    public function test_viewer_can_view_rekap_but_cannot_input_edit_or_delete()
    {
        $surat = SuratTanah::create([
            'nama_dokumen' => 'Surat Test Viewer',
            'nama_sertifikat' => 'Surat Test Viewer',
            'nomor_sertifikat' => 'SHM-VIEWER-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '150',
        ]);

        $this->actingAs($this->viewer)->get(route('brangkas.surat-tanah'))->assertStatus(200);
        $this->actingAs($this->viewer)->get(route('brangkas.akta-notaris'))->assertStatus(200);
        $this->actingAs($this->viewer)->get(route('brangkas.data-aset'))->assertStatus(200);
        $this->actingAs($this->viewer)->get(route('handover.index'))->assertStatus(200);

        $postRes = $this->actingAs($this->viewer)->post(route('brangkas.surat-tanah.store'), [
            'nama_sertifikat' => 'Coba Input Viewer',
            'nomor_sertifikat' => 'SHM-FAIL',
            'jenis_sertifikat' => 'SHM',
            'luas' => '100',
        ]);
        $postRes->assertSessionHas('error');

        $editRes = $this->actingAs($this->viewer)->get(route('brangkas.surat-tanah.edit', $surat->id));
        $editRes->assertSessionHas('error');

        $delRes = $this->actingAs($this->viewer)->post(route('brangkas.surat-tanah.destroy', $surat->id));
        $delRes->assertSessionHas('error');
        $this->assertNotNull(SuratTanah::find($surat->id));

        $hoRes = $this->actingAs($this->viewer)->post(route('handover.store'), [
            'kategori' => 'Arsip Surat Tanah',
            'ref_id' => $surat->id,
            'nama_dokumen' => $surat->nama_sertifikat,
            'status' => 'Dipinjam',
            'tgl_serahterima' => '2026-09-15',
        ]);
        $hoRes->assertSessionHas('error');

        $this->actingAs($this->viewer)->get(route('setting.users'))->assertRedirect(route('handover.index'));

        $surat->delete();
    }

    public function test_aproval_cannot_input_or_delete_brangkas_data()
    {
        $surat = SuratTanah::create([
            'nama_dokumen' => 'Surat Test Aproval',
            'nama_sertifikat' => 'Surat Test Aproval',
            'nomor_sertifikat' => 'SHM-APROVAL-01',
            'jenis_sertifikat' => 'SHM',
            'luas' => '120',
        ]);

        $this->actingAs($this->aproval)->get(route('brangkas.surat-tanah'))->assertStatus(200);

        $postRes = $this->actingAs($this->aproval)->post(route('brangkas.surat-tanah.store'), [
            'nama_sertifikat' => 'Coba Input Aproval',
            'nomor_sertifikat' => 'SHM-FAIL-APROVAL',
            'jenis_sertifikat' => 'SHM',
            'luas' => '100',
        ]);
        $postRes->assertSessionHas('error');

        $delRes = $this->actingAs($this->aproval)->post(route('brangkas.surat-tanah.destroy', $surat->id));
        $delRes->assertSessionHas('error');

        $surat->delete();
    }
}
