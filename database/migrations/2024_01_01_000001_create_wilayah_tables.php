<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('kode', 8)->notNullable();
            $table->string('nama', 64)->notNullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
        });

        Schema::create('kabupaten', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->smallInteger('id_provinsi')->notNullable();
            $table->string('kode', 8)->nullable();
            $table->string('nama', 64)->notNullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
            $table->smallInteger('id_kabupaten')->nullable();
            $table->string('kode_surat', 8)->nullable();

            $table->foreign('id_provinsi')->references('id')->on('provinsi');
        });

        Schema::create('kecamatan', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->smallInteger('id_kabupaten')->notNullable();
            $table->string('kode', 8)->nullable();
            $table->string('nama', 64)->notNullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
            $table->smallInteger('id_kecamatan')->nullable();

            $table->foreign('id_kabupaten')->references('id')->on('kabupaten');
        });

        Schema::create('kelurahan', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->smallInteger('id_kecamatan')->notNullable();
            $table->string('kode', 8)->notNullable();
            $table->string('nama', 64)->notNullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();

            $table->foreign('id_kecamatan')->references('id')->on('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelurahan');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('kabupaten');
        Schema::dropIfExists('provinsi');
    }
};
