<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE surat_tanah MODIFY status_handover VARCHAR(50) DEFAULT 'Tersedia'");
            DB::statement("ALTER TABLE akta_notaris MODIFY status_handover VARCHAR(50) DEFAULT 'Tersedia'");
        }

        Schema::table("surat_tanah", function (Blueprint $table) {
            if (!Schema::hasColumn("surat_tanah", "jenis_sertifikat")) {
                $table->string("jenis_sertifikat")->nullable()->after("id");
            }
            if (!Schema::hasColumn("surat_tanah", "nama_sertifikat")) {
                $table->string("nama_sertifikat")->nullable()->after("jenis_sertifikat");
            }
            if (!Schema::hasColumn("surat_tanah", "alamat")) {
                $table->text("alamat")->nullable()->after("luas");
            }
        });

        Schema::table("akta_notaris", function (Blueprint $table) {
            if (!Schema::hasColumn("akta_notaris", "jenis_sertifikat")) {
                $table->string("jenis_sertifikat")->nullable()->after("id");
            }
            if (!Schema::hasColumn("akta_notaris", "nama_sertifikat")) {
                $table->string("nama_sertifikat")->nullable()->after("jenis_sertifikat");
            }
            if (!Schema::hasColumn("akta_notaris", "nomor_sertifikat")) {
                $table->string("nomor_sertifikat")->nullable()->after("nama_dokumen");
            }
            if (!Schema::hasColumn("akta_notaris", "luas")) {
                $table->string("luas")->nullable()->after("nomor_sertifikat");
            }
            if (!Schema::hasColumn("akta_notaris", "alamat")) {
                $table->text("alamat")->nullable()->after("luas");
            }
        });

        // Copy existing data to new columns if needed
        DB::statement("UPDATE surat_tanah SET nama_sertifikat = nama_dokumen WHERE nama_sertifikat IS NULL");
        DB::statement("UPDATE surat_tanah SET alamat = lokasi WHERE alamat IS NULL AND lokasi IS NOT NULL");

        DB::statement("UPDATE akta_notaris SET nama_sertifikat = nama_dokumen WHERE nama_sertifikat IS NULL");
        DB::statement("UPDATE akta_notaris SET nomor_sertifikat = nomor_akta WHERE nomor_sertifikat IS NULL AND nomor_akta IS NOT NULL");
    }

    public function down(): void
    {
    }
};
