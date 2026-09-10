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
        Schema::create('oss', function (Blueprint $table) {
            $table->id('id_oss');
            $table->unsignedBigInteger('id_user');
            $table->string('kode_unik', 50);
            $table->string('bukti_bayar', 255);
            $table->date('tanggal')->nullable();
            $table->date('tgl_izin')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->enum('status', ['mengisi persyaratan', 'verifikasi', 'perbaikan', 'dokumen diproses', 'izin terbit'])->default('mengisi persyaratan');
            $table->foreign('id_user')->references('id_user')->on('users')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
        Schema::create('oss_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_oss');
            $table->string('statusType', 20);
            $table->string('icon', 50);
            $table->string('textstatus', 50);
            $table->enum('status', ['success', 'failed'])->nullable();
            $table->text('keterangan')->nullable();
            $table->foreign('id_oss')->references('id_oss')->on('oss')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oss_status');
        Schema::dropIfExists('oss');
    }
};
