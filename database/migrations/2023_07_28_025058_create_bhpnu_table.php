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
        Schema::create('bhpnu', function (Blueprint $table) {
            $table->id('id_bhpnu');
            $table->unsignedBigInteger('id_user');
            $table->string('bukti_bayar', 255);
            $table->string('no_resi', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->date('tgl_dikirim')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->enum('status', ['mengisi persyaratan', 'verifikasi', 'perbaikan', 'dokumen diproses', 'dokumen dikirim'])->default('mengisi persyaratan');
            $table->foreign('id_user')->references('id_user')->on('users')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
        Schema::create('bhpnu_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bhpnu');
            $table->string('statusType', 20);
            $table->string('icon', 50);
            $table->string('textstatus', 50);
            $table->enum('status', ['success', 'failed'])->nullable();
            $table->text('keterangan')->nullable();
            $table->foreign('id_bhpnu')->references('id_bhpnu')->on('bhpnu')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bhpnu_status');
        Schema::dropIfExists('bhpnu');
    }
};
