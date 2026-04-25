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
        Schema::create('persil', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomor_persil', 128)->nullable();
            $table->string('klas', 128)->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->string('alamat')->nullable();
            $table->foreignId('id_kelurahan')->references('id')->on('kelurahan');
            $table->foreignId('id_kecamatan')->nullable()->references('id')->on('kecamatan');
            $table->foreignId('id_kabupaten')->nullable()->references('id')->on('kabupaten');
            $table->string('batas_utara', 256)->nullable();
            $table->string('batas_selatan', 256)->nullable();
            $table->string('batas_timur', 256)->nullable();
            $table->string('batas_barat', 256)->nullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
            $table->string('koordinat')->nullable();
            $table->string('legacy_id', 64)->nullable()->unique();
            $table->foreignId('created_by')->nullable()->references('id')->on('users');
            $table->foreignId('verified_by')->nullable()->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persil');
    }
};
