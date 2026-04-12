<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_hak', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 16)->notNullable();
            $table->string('nama', 32)->notNullable();
            $table->string('keterangan', 512)->nullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->default(0);
        });

        Schema::create('jenis_uupa', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('nama', 32)->notNullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->nullable();
        });

        Schema::create('jenis_monitoring', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('nama', 32)->notNullable();
        });

        Schema::create('pengelola', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 255)->notNullable();
            $table->string('keterangan', 512)->nullable();
            $table->string('kontak', 64)->nullable();
            $table->string('no_telepon', 18)->nullable();
            $table->string('email', 64)->nullable();
            $table->string('alamat', 255)->nullable();
        });

        Schema::create('penggunaan_rtr', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 64)->notNullable();
            $table->string('nama_file', 256)->nullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->default(0);
        });

        Schema::create('penggunaan_sg', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('id_penggunaan')->notNullable();
            $table->string('nama', 255)->notNullable();

            $table->foreign('id_penggunaan')->references('id')->on('penggunaan_rtr');
        });

        Schema::create('penggunaan_tanah_desa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 64)->notNullable();
            $table->string('nama_file', 256)->nullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->nullable();
        });

        Schema::create('status_kesesuaian', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 256)->nullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->notNullable()->default(0);
        });

        Schema::create('status_sertifikat', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 256)->nullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->default(0);
        });

        Schema::create('kategori', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('nama', 32)->notNullable();
            $table->string('warna', 12)->nullable();
        });

        Schema::create('kategori_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 32)->notNullable();
        });

        Schema::create('kategori_tanah_desa_detail', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('nama', 32)->notNullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_tanah_desa_detail');
        Schema::dropIfExists('kategori_tanah_desa');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('status_sertifikat');
        Schema::dropIfExists('status_kesesuaian');
        Schema::dropIfExists('penggunaan_tanah_desa');
        Schema::dropIfExists('penggunaan_sg');
        Schema::dropIfExists('penggunaan_rtr');
        Schema::dropIfExists('pengelola');
        Schema::dropIfExists('jenis_monitoring');
        Schema::dropIfExists('jenis_uupa');
        Schema::dropIfExists('jenis_hak');
    }
};
