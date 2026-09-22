<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_surat', ['BPKB', 'STNK']);
            $table->unsignedBigInteger('data_aset_id')->nullable();
            $table->string('nama_kendaraan');
            $table->string('nama_pemilik')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->date('tgl_input')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status_handover', ['Tersedia', 'Dipinjam', 'Diagunkan', 'Dihibahkan'])->default('Tersedia');
            $table->boolean('warna_merah')->default(false);
            $table->timestamps();

            $table->foreign('data_aset_id')->references('id')->on('data_aset_lembaga')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_kendaraan');
    }
};
