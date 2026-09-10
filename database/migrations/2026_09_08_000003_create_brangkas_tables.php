<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("surat_tanah", function (Blueprint $table) {
            $table->id();
            $table->string("nama_dokumen");
            $table->string("nomor_sertifikat")->nullable();
            $table->string("lokasi")->nullable();
            $table->string("luas")->nullable();
            $table->string("atas_nama")->nullable();
            $table->text("keterangan")->nullable();
            $table->enum("status_handover", ["Tersedia","Dipinjam","Diagunkan","Dihibahkan"])->default("Tersedia");
            $table->boolean("warna_merah")->default(false);
            $table->timestamps();
        });

        Schema::create("akta_notaris", function (Blueprint $table) {
            $table->id();
            $table->string("nama_dokumen");
            $table->string("nomor_akta")->nullable();
            $table->string("nama_notaris")->nullable();
            $table->date("tanggal_akta")->nullable();
            $table->string("perihal")->nullable();
            $table->text("keterangan")->nullable();
            $table->enum("status_handover", ["Tersedia","Dipinjam","Diagunkan","Dihibahkan"])->default("Tersedia");
            $table->boolean("warna_merah")->default(false);
            $table->timestamps();
        });

        Schema::create("data_aset_lembaga", function (Blueprint $table) {
            $table->id();
            $table->string("nama_aset");
            $table->string("jenis_aset")->nullable();
            $table->string("nilai_aset")->nullable();
            $table->string("lokasi")->nullable();
            $table->text("keterangan")->nullable();
            $table->enum("status_handover", ["Tersedia","Dipinjam","Diagunkan","Dihibahkan"])->default("Tersedia");
            $table->boolean("warna_merah")->default(false);
            $table->timestamps();
        });

        Schema::create("record_of_handover", function (Blueprint $table) {
            $table->id();
            $table->enum("kategori", ["Arsip Surat Tanah","Akta Notaris","Data Aset Lembaga"]);
            $table->string("nama_dokumen");
            $table->unsignedBigInteger("ref_id");
            $table->enum("status", ["Dipinjam","Diagunkan","Dihibahkan"]);
            $table->string("nama_peminjam")->nullable();
            $table->string("no_telp_peminjam")->nullable();
            $table->string("nama_bank")->nullable();
            $table->string("jangka_agunan")->nullable();
            $table->string("penanggung_agunan")->nullable();
            $table->string("no_telp_penanggung")->nullable();
            $table->string("nama_penerima")->nullable();
            $table->string("no_telp_penerima")->nullable();
            $table->date("tgl_serahterima")->nullable();
            $table->string("file_bukti")->nullable();
            $table->text("catatan")->nullable();
            $table->timestamps();
        });

        Schema::create("keuangan_pengajuan", function (Blueprint $table) {
            $table->id();
            $table->string("judul");
            $table->decimal("jumlah", 15, 2)->nullable();
            $table->text("keterangan")->nullable();
            $table->string("file_bukti")->nullable();
            $table->enum("status", ["Menunggu","Disetujui","Ditolak"])->default("Menunggu");
            $table->timestamps();
        });

        Schema::create("keuangan_dokumen", function (Blueprint $table) {
            $table->id();
            $table->enum("jenis", ["rekening_koran","buku_bank","buku_kas_tunai","buku_kas_umum"]);
            $table->string("periode");
            $table->string("file_path");
            $table->string("judul")->nullable();
            $table->text("keterangan")->nullable();
            $table->timestamps();
        });

        Schema::create("keuangan_rekap_bulanan", function (Blueprint $table) {
            $table->id();
            $table->string("bulan");
            $table->year("tahun");
            $table->decimal("total_pemasukan", 15, 2)->default(0);
            $table->decimal("total_pengeluaran", 15, 2)->default(0);
            $table->decimal("saldo", 15, 2)->default(0);
            $table->text("keterangan")->nullable();
            $table->string("file_rekap")->nullable();
            $table->timestamps();
        });

        Schema::create("keuangan_rekap_tahunan", function (Blueprint $table) {
            $table->id();
            $table->year("tahun");
            $table->decimal("total_pemasukan", 15, 2)->default(0);
            $table->decimal("total_pengeluaran", 15, 2)->default(0);
            $table->decimal("saldo", 15, 2)->default(0);
            $table->text("keterangan")->nullable();
            $table->string("file_rekap")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("keuangan_rekap_tahunan");
        Schema::dropIfExists("keuangan_rekap_bulanan");
        Schema::dropIfExists("keuangan_dokumen");
        Schema::dropIfExists("keuangan_pengajuan");
        Schema::dropIfExists("record_of_handover");
        Schema::dropIfExists("data_aset_lembaga");
        Schema::dropIfExists("akta_notaris");
        Schema::dropIfExists("surat_tanah");
    }
};
