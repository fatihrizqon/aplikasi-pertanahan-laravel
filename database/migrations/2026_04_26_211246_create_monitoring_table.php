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
        Schema::create('jenis_monitoring', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->timestamps();
        });

        Schema::create('monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_persil')->references('id')->on('persil');
            $table->foreignId('id_jenis_monitoring')->nullable()->references('id')->on('jenis_monitoring');
            $table->foreignId('id_file')->nullable()->references('id')->on('files');
            $table->foreignId('id_file_pendukung')->nullable()->references('id')->on('files');
            $table->text('hasil')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring');
        Schema::dropIfExists('jenis_monitoring');
    }
};
