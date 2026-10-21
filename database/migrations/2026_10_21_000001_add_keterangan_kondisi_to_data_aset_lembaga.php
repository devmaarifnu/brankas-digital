<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('data_aset_lembaga', 'keterangan_kondisi')) {
            Schema::table('data_aset_lembaga', function (Blueprint $table) {
                $table->text('keterangan_kondisi')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('data_aset_lembaga', 'keterangan_kondisi')) {
            Schema::table('data_aset_lembaga', function (Blueprint $table) {
                $table->dropColumn('keterangan_kondisi');
            });
        }
    }
};
