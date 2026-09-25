<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
            
            // Temporary table copy
            DB::statement('CREATE TABLE record_of_handover_temp AS SELECT * FROM record_of_handover;');
            DB::statement('DROP TABLE record_of_handover;');

            Schema::create('record_of_handover', function (Blueprint $table) {
                $table->id();
                $table->string('kategori');
                $table->string('nama_dokumen');
                $table->unsignedBigInteger('ref_id');
                $table->string('status');
                $table->string('nama_peminjam')->nullable();
                $table->string('no_telp_peminjam')->nullable();
                $table->string('nama_bank')->nullable();
                $table->string('jangka_agunan')->nullable();
                $table->string('penanggung_agunan')->nullable();
                $table->string('no_telp_penanggung')->nullable();
                $table->string('nama_penerima')->nullable();
                $table->string('no_telp_penerima')->nullable();
                $table->date('tgl_serahterima')->nullable();
                $table->string('file_bukti')->nullable();
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nama_petugas')->nullable();
                $table->timestamps();
            });

            // Check columns of temp table vs new table before insert
            $hasUserId = Schema::hasColumn('record_of_handover_temp', 'user_id');
            $hasPetugas = Schema::hasColumn('record_of_handover_temp', 'nama_petugas');

            if ($hasUserId && $hasPetugas) {
                DB::statement('INSERT INTO record_of_handover (id, kategori, nama_dokumen, ref_id, status, nama_peminjam, no_telp_peminjam, nama_bank, jangka_agunan, penanggung_agunan, no_telp_penanggung, nama_penerima, no_telp_penerima, tgl_serahterima, file_bukti, catatan, user_id, nama_petugas, created_at, updated_at) SELECT id, kategori, nama_dokumen, ref_id, status, nama_peminjam, no_telp_peminjam, nama_bank, jangka_agunan, penanggung_agunan, no_telp_penanggung, nama_penerima, no_telp_penerima, tgl_serahterima, file_bukti, catatan, user_id, nama_petugas, created_at, updated_at FROM record_of_handover_temp;');
            } else {
                DB::statement('INSERT INTO record_of_handover (id, kategori, nama_dokumen, ref_id, status, nama_peminjam, no_telp_peminjam, nama_bank, jangka_agunan, penanggung_agunan, no_telp_penanggung, nama_penerima, no_telp_penerima, tgl_serahterima, file_bukti, catatan, created_at, updated_at) SELECT id, kategori, nama_dokumen, ref_id, status, nama_peminjam, no_telp_peminjam, nama_bank, jangka_agunan, penanggung_agunan, no_telp_penanggung, nama_penerima, no_telp_penerima, tgl_serahterima, file_bukti, catatan, created_at, updated_at FROM record_of_handover_temp;');
            }

            DB::statement('DROP TABLE record_of_handover_temp;');
            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            Schema::table('record_of_handover', function (Blueprint $table) {
                $table->string('kategori')->change();
                $table->string('status')->change();
            });
        }
    }

    public function down(): void
    {
    }
};
