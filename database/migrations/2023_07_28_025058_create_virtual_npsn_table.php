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
        Schema::create('virtual_npsn', function (Blueprint $table) {
            $table->id('id_npsn');
            $table->unsignedBigInteger('id_jenjang');
            $table->unsignedBigInteger('id_prov');
            $table->unsignedBigInteger('id_kab');
            $table->string('nomor_virtual', 20)->nullable();
            $table->string('nik_kepsek', 15)->nullable()->unique();
            $table->string('nama_sekolah', 50);
            $table->string('alamat', 255);
            $table->string('email', 100);
            $table->datetime('accepted_date')->nullable();
            $table->datetime('actived_date')->nullable();
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjang_pendidikan')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_prov')->references('id_prov')->on('provinsi')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_kab')->references('id_kab')->on('kabupaten')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_npsn');
    }
};
