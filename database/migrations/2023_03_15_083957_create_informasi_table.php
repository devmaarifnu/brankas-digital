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
        Schema::create('informasi', function (Blueprint $table) {
            $table->id('id_info');
            $table->string('slug', 255)->unique();
            $table->enum('type', ['SK', 'Piagam', 'Berita', 'Pengumuman']);
            $table->string('headline', 255);
            $table->dateTime('tgl_upload');
            $table->text('content');
            $table->string('image', 255);
            $table->string('tag', 100)->nullable();
            $table->timestamps();
        });
        Schema::create('informasi_file', function (Blueprint $table) {
            $table->id('id_file');
            $table->unsignedBigInteger('id_info');
            $table->foreign('id_info')->references('id_info')->on('informasi')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->string('fileupload', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi_file');
        Schema::dropIfExists('informasi');
    }
};
