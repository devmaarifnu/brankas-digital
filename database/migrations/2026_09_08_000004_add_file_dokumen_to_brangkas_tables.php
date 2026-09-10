<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("surat_tanah", function (Blueprint $table) {
            $table->string("file_dokumen")->nullable()->after("keterangan");
        });

        Schema::table("akta_notaris", function (Blueprint $table) {
            $table->string("file_dokumen")->nullable()->after("keterangan");
        });
    }

    public function down(): void
    {
        Schema::table("surat_tanah", function (Blueprint $table) {
            $table->dropColumn("file_dokumen");
        });

        Schema::table("akta_notaris", function (Blueprint $table) {
            $table->dropColumn("file_dokumen");
        });
    }
};
