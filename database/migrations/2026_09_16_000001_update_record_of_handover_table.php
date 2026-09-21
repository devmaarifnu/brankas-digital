<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('record_of_handover')) {
            Schema::table('record_of_handover', function (Blueprint $table) {
                if (!Schema::hasColumn('record_of_handover', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')->references('id_user')->on('users')->onDelete('set null');
                }
            });

            // Modify status column to varchar to comfortably support 'Dikembalikan'
            Schema::table('record_of_handover', function (Blueprint $table) {
                $table->string('status', 50)->change();
            });
        }

        if (Schema::hasTable('surat_tanah')) {
            Schema::table('surat_tanah', function (Blueprint $table) {
                if (Schema::hasColumn('surat_tanah', 'status_handover')) {
                    $table->string('status_handover', 50)->default('Tersedia')->change();
                }
            });
        }

        if (Schema::hasTable('akta_notaris')) {
            Schema::table('akta_notaris', function (Blueprint $table) {
                if (Schema::hasColumn('akta_notaris', 'status_handover')) {
                    $table->string('status_handover', 50)->default('Tersedia')->change();
                }
            });
        }

        if (Schema::hasTable('data_aset_lembaga')) {
            Schema::table('data_aset_lembaga', function (Blueprint $table) {
                if (Schema::hasColumn('data_aset_lembaga', 'status_handover')) {
                    $table->string('status_handover', 50)->default('Tersedia')->change();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('record_of_handover')) {
            Schema::table('record_of_handover', function (Blueprint $table) {
                if (Schema::hasColumn('record_of_handover', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
