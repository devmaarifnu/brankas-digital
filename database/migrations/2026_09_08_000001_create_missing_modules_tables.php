<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. tahun_pelajaran
        if (!Schema::hasTable('tahun_pelajaran')) {
            Schema::create('tahun_pelajaran', function (Blueprint $table) {
                $table->id();
                $table->string('tapel_dapo', 50)->unique();
                $table->string('nama_tapel', 100);
            });

            DB::table('tahun_pelajaran')->insert([
                ['tapel_dapo' => '2023/2024 Genap', 'nama_tapel' => '2023/2024 Genap'],
                ['tapel_dapo' => '2024/2025 Ganjil', 'nama_tapel' => '2024/2025 Ganjil'],
                ['tapel_dapo' => '2024/2025 Genap', 'nama_tapel' => '2024/2025 Genap'],
            ]);
        }

        // 2. pdptk
        if (!Schema::hasTable('pdptk')) {
            Schema::create('pdptk', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_satpen');
                $table->string('tapel', 50)->nullable();
                $table->integer('pd_lk')->default(0);
                $table->integer('pd_pr')->default(0);
                $table->integer('jml_pd')->default(0);
                $table->integer('guru_lk')->default(0);
                $table->integer('guru_pr')->default(0);
                $table->integer('jml_guru')->default(0);
                $table->integer('tendik_lk')->default(0);
                $table->integer('tendik_pr')->default(0);
                $table->integer('jml_tendik')->default(0);
                $table->dateTime('last_sinkron')->nullable();
                $table->tinyInteger('status_sinkron')->default(1);

                $table->index('id_satpen');
                $table->index('tapel');
            });
        }

        // 3. data_lainnya
        if (!Schema::hasTable('data_lainnya')) {
            Schema::create('data_lainnya', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_satpen');
                $table->string('npyp', 100)->nullable();
                $table->string('naungan', 100)->nullable();
                $table->string('no_sk_pendirian', 150)->nullable();
                $table->date('tgl_sk_pendirian')->nullable();
                $table->string('no_sk_operasional', 150)->nullable();
                $table->date('tgl_sk_operasional')->nullable();
                $table->text('link_sk_operational')->nullable();
                $table->string('akreditasi', 50)->nullable();
                $table->string('website', 255)->nullable();
                $table->text('lingkungan_satpen')->nullable();
                $table->dateTime('last_sinkron')->nullable();
                $table->tinyInteger('status_sinkron')->default(1);

                $table->index('id_satpen');
            });
        }

        // 4. npyp
        if (!Schema::hasTable('npyp')) {
            Schema::create('npyp', function (Blueprint $table) {
                $table->id('id_npyp');
                $table->unsignedBigInteger('id_pw')->nullable();
                $table->unsignedBigInteger('id_pc')->nullable();
                $table->string('nomor_npyp', 100)->nullable();
                $table->string('nama_npyp', 255)->nullable();
                $table->string('nama_operator', 255)->nullable();
                $table->string('nomor_operator', 50)->nullable();
                $table->timestamps();
            });
        }

        // 5. npyp_satpen
        if (!Schema::hasTable('npyp_satpen')) {
            Schema::create('npyp_satpen', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_npyp');
                $table->unsignedBigInteger('id_satpen');
                $table->date('assign_date')->nullable();
                $table->timestamps();

                $table->index('id_npyp');
                $table->index('id_satpen');
            });
        }

        // 6. ptk
        if (!Schema::hasTable('ptk')) {
            Schema::create('ptk', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_npyp')->nullable();
                $table->unsignedBigInteger('id_satpen');
                $table->string('nik', 30);
                $table->string('nama_ptk', 255);
                $table->string('tempat_lahir', 100)->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->string('jenis_kelamin', 20)->nullable();
                $table->string('nama_ibu', 255)->nullable();
                $table->string('agama', 30)->nullable();
                $table->string('kebutuhan_khusus', 100)->nullable();
                $table->string('status_perkawinan', 50)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('kabupaten_kota', 100)->nullable();
                $table->string('kecamatan', 100)->nullable();
                $table->string('desa_kelurahan', 100)->nullable();
                $table->text('alamat')->nullable();
                $table->string('kode_pos', 10)->nullable();
                $table->string('jenis_ptk', 100)->nullable();
                $table->string('status_kepegawaian', 100)->nullable();
                $table->string('nip', 50)->nullable();
                $table->string('lembaga_pengangkat', 100)->nullable();
                $table->string('no_sk_pengangkatan', 150)->nullable();
                $table->date('tmt_pengangkatan')->nullable();
                $table->string('sumber_gaji', 100)->nullable();
                $table->string('lisensi_kepala_sekolah', 50)->nullable();
                $table->string('nomor_surat_tugas', 150)->nullable();
                $table->date('tanggal_surat_tugas')->nullable();
                $table->date('tmt_tugas')->nullable();
                $table->string('upload_sk', 255)->nullable();
                $table->string('status_ajuan', 50)->default('verifikasi');
                $table->dateTime('tanggal_verifikasi')->nullable();
                $table->dateTime('tanggal_revisi')->nullable();
                $table->dateTime('tanggal_proses')->nullable();
                $table->dateTime('tanggal_approve')->nullable();
                $table->dateTime('tanggal_dikeluarkan')->nullable();
                $table->text('catatan_verifikasi')->nullable();
                $table->text('catatan_revisi')->nullable();
                $table->text('catatan_proses')->nullable();
                $table->text('catatan_approve')->nullable();
                $table->text('catatan_dikeluarkan')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->string('verifikator_name', 255)->nullable();
                $table->unsignedBigInteger('verifikator_id')->nullable();
                $table->unsignedBigInteger('approver_id')->nullable();
                $table->text('keterangan_revisi')->nullable();
                $table->string('nomor_sk_keluar', 150)->nullable();
                $table->text('catatan_verifikator')->nullable();
                $table->timestamps();

                $table->index('id_satpen');
                $table->index('id_npyp');
                $table->index('status_ajuan');
            });
        }

        // 7. ptk_status_history
        if (!Schema::hasTable('ptk_status_history')) {
            Schema::create('ptk_status_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ptk_id');
                $table->string('status_from', 50)->nullable();
                $table->string('status_to', 50);
                $table->text('keterangan')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('ptk_id');
            });
        }

        // 8. coretax
        if (!Schema::hasTable('coretax')) {
            Schema::create('coretax', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_pw')->nullable();
                $table->unsignedBigInteger('id_pc')->nullable();
                $table->string('nitku', 50)->nullable();
                $table->string('nama_pic', 255)->nullable();
                $table->string('nik_pic', 30)->nullable();
                $table->string('whatsapp_pic', 30)->nullable();
                $table->dateTime('tanggal')->nullable();
                $table->dateTime('tgl_submit')->nullable();
                $table->dateTime('tgl_acc')->nullable();
                $table->dateTime('tgl_expiry')->nullable();
                $table->string('new_request', 50)->nullable();
                $table->string('npwp_lama', 50)->nullable();
                $table->string('status', 50)->default('menunggu');
                $table->timestamps();

                $table->index('id_user');
            });
        }

        // 9. coretax_status
        if (!Schema::hasTable('coretax_status')) {
            Schema::create('coretax_status', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_coretax');
                $table->string('statusType', 50)->nullable();
                $table->string('icon', 50)->nullable();
                $table->string('textstatus', 100)->nullable();
                $table->string('status', 50)->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->index('id_coretax');
            });
        }

        // 10. profile_pengurus_cabang
        if (!Schema::hasTable('profile_pengurus_cabang')) {
            Schema::create('profile_pengurus_cabang', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_pc');
                $table->text('alamat')->nullable();
                $table->string('kelurahan', 100)->nullable();
                $table->string('kecamatan', 100)->nullable();
                $table->string('kabupaten', 100)->nullable();
                $table->string('lintang', 50)->nullable();
                $table->string('bujur', 50)->nullable();
                $table->string('website', 255)->nullable();
                $table->string('ketua', 255)->nullable();
                $table->string('wakil_ketua', 255)->nullable();
                $table->string('bendahara', 255)->nullable();
                $table->string('sekretaris', 255)->nullable();
                $table->string('telp_ketua', 50)->nullable();
                $table->string('telp_wakil', 50)->nullable();
                $table->string('telp_bendahara', 50)->nullable();
                $table->string('telp_sekretaris', 50)->nullable();
                $table->string('masa_khidmat', 50)->nullable();

                $table->index('id_pc');
            });
        }

        // 11. profile_pengurus_wilayah
        if (!Schema::hasTable('profile_pengurus_wilayah')) {
            Schema::create('profile_pengurus_wilayah', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_pw');
                $table->text('alamat')->nullable();
                $table->string('kelurahan', 100)->nullable();
                $table->string('kecamatan', 100)->nullable();
                $table->string('kabupaten', 100)->nullable();
                $table->string('lintang', 50)->nullable();
                $table->string('bujur', 50)->nullable();
                $table->string('website', 255)->nullable();
                $table->string('ketua', 255)->nullable();
                $table->string('wakil_ketua', 255)->nullable();
                $table->string('bendahara', 255)->nullable();
                $table->string('sekretaris', 255)->nullable();
                $table->string('telp_ketua', 50)->nullable();
                $table->string('telp_wakil', 50)->nullable();
                $table->string('telp_bendahara', 50)->nullable();
                $table->string('telp_sekretaris', 50)->nullable();
                $table->string('masa_khidmat', 50)->nullable();

                $table->index('id_pw');
            });
        }

        // 12. oss_timeline
        if (!Schema::hasTable('oss_timeline')) {
            Schema::create('oss_timeline', function (Blueprint $table) {
                $table->id('id_timeline');
                $table->unsignedBigInteger('id_oss');
                $table->string('status_verifikasi', 50)->nullable();
                $table->dateTime('tgl_verifikasi')->nullable();
                $table->text('catatan')->nullable();
                $table->text('link_pnbp')->nullable();
                $table->text('link_catatan_pupr')->nullable();
                $table->text('link_gistaru')->nullable();
                $table->text('link_izin_terbit')->nullable();
                $table->string('nomor_ku', 100)->nullable();
                $table->timestamps();

                $table->index('id_oss');
            });
        }

        // 13. activity_logs
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 50)->nullable();
                $table->string('table_name', 100)->nullable();
                $table->string('menu_name', 100)->nullable();
                $table->string('record_id', 50)->nullable();
                $table->json('changes')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('url')->nullable();
                $table->string('route', 150)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index('user_id');
            });
        }

        // 14. Ensure default settings exist
        $defaultSettings = [
            ['describe' => 'JUMLAH SATPEN PERHALAMAN', 'lookup' => 'count_perpage', 'value' => '25'],
            ['describe' => 'AWALAN PIAGAM', 'lookup' => 'prefix_piagam_name', 'value' => "Piagam Nomor Registrasi Ma'arif - "],
            ['describe' => 'AWALAN SK', 'lookup' => 'prefix_sk_name', 'value' => "SK Satuan Pendidikan BHPNU - "],
            ['describe' => 'TEMPLATE PIAGAM', 'lookup' => 'template_piagam', 'value' => 'Piagam_Template.docx'],
            ['describe' => 'TEMPLATE SK', 'lookup' => 'template_sk', 'value' => 'SK_Template.docx'],
            ['describe' => 'FORM OSS', 'lookup' => 'oss_form', 'value' => 'https://docs.google.com/forms/'],
            ['describe' => 'SPREADSHEET OSS', 'lookup' => 'oss_spreadsheet', 'value' => 'https://docs.google.com/spreadsheets/'],
            ['describe' => 'TAHUN PELAJARAN AKTIF', 'lookup' => 'current_tapel', 'value' => '2024/2025 Ganjil'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['lookup' => $setting['lookup']],
                $setting
            );
        }

        // 15. Ensure every satpen has pdptk & data_lainnya seeded
        $satpens = DB::table('satpen')->get();
        foreach ($satpens as $satpen) {
            DB::table('pdptk')->updateOrInsert(
                ['id_satpen' => $satpen->id_satpen],
                [
                    'tapel' => '2024/2025 Ganjil',
                    'pd_lk' => 50,
                    'pd_pr' => 50,
                    'jml_pd' => 100,
                    'guru_lk' => 5,
                    'guru_pr' => 5,
                    'jml_guru' => 10,
                    'tendik_lk' => 2,
                    'tendik_pr' => 2,
                    'jml_tendik' => 4,
                    'last_sinkron' => now(),
                    'status_sinkron' => 1,
                ]
            );

            DB::table('data_lainnya')->updateOrInsert(
                ['id_satpen' => $satpen->id_satpen],
                [
                    'npyp' => '12345678',
                    'naungan' => 'LP Ma\'arif NU',
                    'no_sk_pendirian' => 'SK-001/LPM/2020',
                    'tgl_sk_pendirian' => '2020-01-01',
                    'no_sk_operasional' => 'SK-OP-001/2020',
                    'tgl_sk_operasional' => '2020-02-01',
                    'akreditasi' => 'A',
                    'last_sinkron' => now(),
                    'status_sinkron' => 1,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('oss_timeline');
        Schema::dropIfExists('profile_pengurus_wilayah');
        Schema::dropIfExists('profile_pengurus_cabang');
        Schema::dropIfExists('coretax_status');
        Schema::dropIfExists('coretax');
        Schema::dropIfExists('ptk_status_history');
        Schema::dropIfExists('ptk');
        Schema::dropIfExists('npyp_satpen');
        Schema::dropIfExists('npyp');
        Schema::dropIfExists('data_lainnya');
        Schema::dropIfExists('pdptk');
        Schema::dropIfExists('tahun_pelajaran');
    }
};
