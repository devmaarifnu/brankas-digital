<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Tabel Ketegori Satuan Pendidikan (A, B, C, D)
         */
        Schema::create('kategori_satpen', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->enum('nm_kategori', ['A', 'B', 'C', 'D']);
            $table->string('konotasi', 100);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
        /**
         * Tabel Jenjang Pendidikan (TK, RA, SD, SMP, dst.)
         */
        Schema::create('jenjang_pendidikan', function (Blueprint $table) {
            $table->id('id_jenjang');
            $table->string('nm_jenjang', 45);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
        /**
         * Tabel Provinsi (kode_prov_kd => kode propinsi sesuai kemendikbud)
         */
        Schema::create('provinsi', function (Blueprint $table) {
            $table->id('id_prov');
            $table->string('map', 10)->nullable();
            $table->string('kode_prov', 10);
            $table->string('nm_prov', 100);
            $table->timestamps();
        });
        /**
         * Tabel Kabupaten (kode_kab_kd => kode kabupaten susuai kemendikbud)
         */
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->id('id_kab');
            $table->unsignedBigInteger('id_prov');
            $table->string('nama_kab', 255);
            $table->foreign('id_prov')->references('id_prov')->on('provinsi')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
        /**
         * Tabel Pengurus Cabang NU. kode kabupaten untuk nomor registrasi dari data ini,
         * serta untuk pilihan pc pada upload surat
         */
        Schema::create('pengurus_cabang', function (Blueprint $table) {
            $table->id('id_pc');
            $table->unsignedBigInteger('id_prov');
            $table->string('kode_kab', 10);
            $table->string('nama_pc', 255);
            $table->foreign('id_prov')->references('id_prov')->on('provinsi')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
        /**
         * Tabel Satuan Pendidikan
         */
        Schema::create('satpen', function (Blueprint $table) {
            $table->id('id_satpen');
            /**
             * Foregein key
             */
            $table->unsignedBigInteger('id_user')->unique();
            $table->unsignedBigInteger('id_prov');
            $table->unsignedBigInteger('id_kab');
            $table->unsignedBigInteger('id_pc');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->unsignedBigInteger('id_jenjang');

            $table->string('npsn', 45)->unique();
            $table->string('no_registrasi', 45)->unique();
            $table->string('no_urut', 10)->unique();
            $table->string('nm_satpen', 255);
            $table->string('yayasan', 255);
            $table->string('kepsek', 100)->nullable();
            $table->string('telpon', 15)->nullable();
            $table->string('fax', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->year('thn_berdiri')->nullable();
            $table->string('kecamatan', 255);
            $table->string('kelurahan', 255);
            $table->text('alamat');
            $table->string('aset_tanah', 45)->nullable();
            $table->string('nm_pemilik', 100)->nullable();
            $table->dateTime('tgl_registrasi');
            $table->dateTime('actived_date')->nullable();
            $table->enum('status', ['permohonan', 'revisi', 'proses dokumen', 'setujui', 'expired', 'perpanjangan']);

            $table->foreign('id_user')->references('id_user')->on('users')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_prov')->references('id_prov')->on('provinsi')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_kab')->references('id_kab')->on('kabupaten')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_pc')->references('id_pc')->on('pengurus_cabang')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_satpen')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjang_pendidikan')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
        /**
         * Tabel untuk File Surat Permohonan, Rekomendasi
         */
        Schema::create('file_register', function (Blueprint $table) {
            $table->id('id_file');
            $table->unsignedBigInteger('id_satpen');
            $table->string('daerah', 100)->nullable();
            $table->enum('mapfile', ['surat_permohonan', 'rekom_pc', 'rekom_pw']);
            $table->string('nm_lembaga', 50);
            $table->string('nomor_surat', 255);
            $table->date('tgl_surat');
            $table->string('filesurat', 255);
            $table->foreign('id_satpen')->references('id_satpen')->on('satpen')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });

        /**
         * Tabel Timeline Registrasi
         */
        Schema::create('timeline_reg', function (Blueprint $table) {
            $table->id('id_timeline');
            $table->unsignedBigInteger('id_satpen');
            $table->string('status_verifikasi', 45);
            $table->dateTime('tgl_status');
            $table->text('keterangan')->nullable();
            $table->foreign('id_satpen')->references('id_satpen')->on('satpen')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
        /**
         * Tabel File Upload
         */
        Schema::create('file_upload', function (Blueprint $table) {
            $table->id('id_file');
            $table->unsignedBigInteger('id_satpen');
            $table->enum('typefile', ['sk', 'piagam']);
            $table->string('qrcode', 255)->nullable();
            $table->string('nm_file', 255);
            $table->date('tgl_file');
            $table->foreign('id_satpen')->references('id_satpen')->on('satpen')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_upload');
        Schema::dropIfExists('timeline_reg');
        Schema::dropIfExists('file_register');
        Schema::dropIfExists('satpen');
        Schema::dropIfExists('pengurus_cabang');
        Schema::dropIfExists('kabupaten');
        Schema::dropIfExists('provinsi');
        Schema::dropIfExists('jenjang_pendidikan');
        Schema::dropIfExists('kategori_satpen');
    }
};
