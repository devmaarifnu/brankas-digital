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
        // 1. SURAT TANAH
        Schema::table('surat_tanah', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_tanah', 'desa_kelurahan')) $table->string('desa_kelurahan', 100)->nullable();
            if (!Schema::hasColumn('surat_tanah', 'kecamatan')) $table->string('kecamatan', 100)->nullable();
            if (!Schema::hasColumn('surat_tanah', 'kabupaten_kota')) $table->string('kabupaten_kota', 100)->nullable();
            if (!Schema::hasColumn('surat_tanah', 'provinsi')) $table->string('provinsi', 100)->nullable();
            if (!Schema::hasColumn('surat_tanah', 'nama_petugas')) $table->string('nama_petugas', 100)->nullable();
            if (!Schema::hasColumn('surat_tanah', 'tgl_input')) $table->date('tgl_input')->nullable();
        });

        // 2. AKTA NOTARIS
        Schema::table('akta_notaris', function (Blueprint $table) {
            if (!Schema::hasColumn('akta_notaris', 'jenis_dokumen')) $table->string('jenis_dokumen', 100)->nullable();
            if (!Schema::hasColumn('akta_notaris', 'nomor_dokumen')) $table->string('nomor_dokumen', 100)->nullable();
            if (!Schema::hasColumn('akta_notaris', 'tgl_dokumen')) $table->date('tgl_dokumen')->nullable();
            if (!Schema::hasColumn('akta_notaris', 'alamat_notaris')) $table->text('alamat_notaris')->nullable();
            if (!Schema::hasColumn('akta_notaris', 'telp_notaris')) $table->string('telp_notaris', 50)->nullable();
            if (!Schema::hasColumn('akta_notaris', 'nama_petugas')) $table->string('nama_petugas', 100)->nullable();
            if (!Schema::hasColumn('akta_notaris', 'tgl_input')) $table->date('tgl_input')->nullable();
        });

        // 3. DATA ASET LEMBAGA
        Schema::table('data_aset_lembaga', function (Blueprint $table) {
            if (!Schema::hasColumn('data_aset_lembaga', 'jenis_barang')) $table->string('jenis_barang', 100)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'nama_barang')) $table->string('nama_barang', 255)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'merek')) $table->string('merek', 100)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'nomor_seri_model')) $table->string('nomor_seri_model', 100)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'nomor_registrasi')) $table->string('nomor_registrasi', 100)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'sumber_perolehan')) $table->string('sumber_perolehan', 50)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'tgl_perolehan')) $table->date('tgl_perolehan')->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'kondisi_aset')) $table->string('kondisi_aset', 50)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'posisi_aset')) $table->string('posisi_aset', 50)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'nama_ruangan')) $table->string('nama_ruangan', 100)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'nama_penerima')) $table->string('nama_penerima', 100)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'no_telp_penerima')) $table->string('no_telp_penerima', 50)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'file_dokumen')) $table->string('file_dokumen', 255)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'nama_petugas')) $table->string('nama_petugas', 100)->nullable();
            if (!Schema::hasColumn('data_aset_lembaga', 'tgl_input')) $table->date('tgl_input')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_tanah', function (Blueprint $table) {
            $table->dropColumn(['desa_kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi', 'nama_petugas', 'tgl_input']);
        });

        Schema::table('akta_notaris', function (Blueprint $table) {
            $table->dropColumn(['jenis_dokumen', 'nomor_dokumen', 'tgl_dokumen', 'alamat_notaris', 'telp_notaris', 'nama_petugas', 'tgl_input']);
        });

        Schema::table('data_aset_lembaga', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_barang', 'nama_barang', 'merek', 'nomor_seri_model', 'nomor_registrasi',
                'sumber_perolehan', 'tgl_perolehan', 'kondisi_aset', 'posisi_aset',
                'nama_ruangan', 'nama_penerima', 'no_telp_penerima', 'file_dokumen', 'nama_petugas', 'tgl_input'
            ]);
        });
    }
};
