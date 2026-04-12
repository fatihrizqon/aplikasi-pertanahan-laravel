<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pengajuan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50)->notNullable();
        });

        Schema::create('jenis_permohonan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50)->notNullable();
        });

        Schema::create('status_pengajuan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 100)->notNullable();
            $table->string('warna', 10)->nullable();
        });

        Schema::create('kepemilikan_tanah', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50)->notNullable();
            $table->smallInteger('jenis')->nullable();
        });

        Schema::create('tujuan_permohonan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50)->notNullable();
        });

        Schema::create('kondisi_lahan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50)->notNullable();
        });

        Schema::create('masa_berlaku', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50)->notNullable();
        });

        Schema::create('lampiran_kategori', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50)->notNullable();
        });

        Schema::create('lampiran_jenis', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('id_lampiran_kategori')->notNullable();
            $table->string('nama', 100)->notNullable();
            $table->string('hint', 100)->nullable();

            $table->foreign('id_lampiran_kategori')->references('id')->on('lampiran_kategori');
        });

        Schema::create('lampiran_jenis_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('id_lampiran_jenis')->notNullable();
            $table->smallInteger('id_tujuan_permohonan')->notNullable();

            $table->foreign('id_lampiran_jenis')->references('id')->on('lampiran_jenis');
            $table->foreign('id_tujuan_permohonan')->references('id')->on('tujuan_permohonan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampiran_jenis_tanah_desa');
        Schema::dropIfExists('lampiran_jenis');
        Schema::dropIfExists('lampiran_kategori');
        Schema::dropIfExists('masa_berlaku');
        Schema::dropIfExists('kondisi_lahan');
        Schema::dropIfExists('tujuan_permohonan');
        Schema::dropIfExists('kepemilikan_tanah');
        Schema::dropIfExists('status_pengajuan');
        Schema::dropIfExists('jenis_permohonan');
        Schema::dropIfExists('jenis_pengajuan');
    }
};
