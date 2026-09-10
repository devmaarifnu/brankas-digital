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
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id('id_shorturl');
            $table->string('title')->nullable();
            $table->string('short_code', 50)->unique();
            $table->text('original_url');
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();

            $table->index('short_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
