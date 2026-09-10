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
        /**
         * Tabel User Login
         */
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('name', 100)->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['super admin', 'admin pusat', 'admin wilayah', 'admin cabang', 'operator']);
            $table->enum('status_active', ['active', 'block']);
            $table->string('provId', 20)->nullable();
            $table->string('cabangId', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
