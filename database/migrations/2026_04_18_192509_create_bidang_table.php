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
        Schema::create('bidang', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('id_persil')->references('id')->on('persil');
            $table->foreignId('id_jenis_hak')->references('id')->on('jenis_hak');
            $table->foreignId('id_jenis_hak_adat')->references('id')->on('jenis_hak_adat');
            $table->foreignId('id_kategori')->references('id')->on('kategori');
            $table->foreignId('id_status_kesesuaian')->references('id')->on('status_kesesuaian');
            $table->foreignId('id_pengelola')->references('id')->on('pengelola');
            $table->foreignId('id_penggunaan')->references('id')->on('penggunaan');
            $table->enum('pemilik', ['kasultanan', 'kadipaten'])->nullable();
            $table->string('nomor_hak')->nullable();
            $table->string('nomor_hak_adat')->nullable();
            $table->string('nomor_bidang')->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
            $table->string('koordinat')->nullable();
            $table->foreignId('id_file')->references('id')->on('files')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('created_by')->references('id')->on('users');
            $table->foreignId('verified_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidang');
    }
};
