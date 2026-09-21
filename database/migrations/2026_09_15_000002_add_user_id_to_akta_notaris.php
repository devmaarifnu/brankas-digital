<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('akta_notaris', function (Blueprint $table) {
            if (!Schema::hasColumn('akta_notaris', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('file_dokumen');
                $table->foreign('user_id')->references('id_user')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('akta_notaris', function (Blueprint $table) {
            if (Schema::hasColumn('akta_notaris', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
