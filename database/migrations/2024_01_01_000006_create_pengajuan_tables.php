<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomor', 20)->notNullable();
            $table->date('tgl_masuk')->nullable();
            $table->string('nama', 100)->nullable();
            $table->string('nama_instansi', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->smallInteger('id_jenis_permohonan')->nullable();
            $table->smallInteger('id_kepemilikan_tanah')->nullable();
            $table->string('lokasi', 150)->nullable();
            $table->smallInteger('id_kelurahan')->nullable();
            $table->string('persil', 64)->nullable();
            $table->string('bidang', 20)->nullable();
            $table->string('sub_persil', 20)->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->integer('id_penggunaan')->nullable();
            $table->smallInteger('diwakilkan')->nullable();
            $table->string('nama_wakil', 20)->nullable();
            $table->text('alamat_wakil')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->smallInteger('id_status_pengajuan')->nullable();
            $table->smallInteger('id_jenis_pengajuan')->nullable();
            $table->string('no_kekancingan', 64)->nullable();
            $table->string('keterangan', 512)->nullable();

            $table->foreign('id_jenis_permohonan')->references('id')->on('jenis_permohonan');
            $table->foreign('id_kepemilikan_tanah')->references('id')->on('kepemilikan_tanah');
            $table->foreign('id_kelurahan')->references('id')->on('kelurahan');
            $table->foreign('id_penggunaan')->references('id')->on('penggunaan_rtr');
            $table->foreign('id_status_pengajuan')->references('id')->on('status_pengajuan');
            $table->foreign('id_jenis_pengajuan')->references('id')->on('jenis_pengajuan');
        });

        Schema::create('pengajuan_tanah_desa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomor', 20)->notNullable();
            $table->date('tgl_masuk')->nullable();
            $table->string('nama', 100)->nullable();
            $table->string('nama_instansi', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->smallInteger('id_tujuan_permohonan')->nullable();
            $table->smallInteger('id_jenis_permohonan')->nullable();
            $table->smallInteger('id_kondisi_lahan')->nullable();
            $table->smallInteger('id_masa_berlaku')->nullable();
            $table->smallInteger('id_kepemilikan_tanah')->nullable();
            $table->string('lokasi', 150)->nullable();
            $table->smallInteger('id_kelurahan')->nullable();
            $table->string('persil', 64)->nullable();
            $table->string('bidang', 20)->nullable();
            $table->string('sub_persil', 20)->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->integer('id_penggunaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('longitude', 255)->nullable();
            $table->string('latitude', 255)->nullable();
            $table->smallInteger('diwakilkan')->nullable();
            $table->string('nama_wakil', 20)->nullable();
            $table->text('alamat_wakil')->nullable();
            $table->smallInteger('id_jenis_pengajuan')->nullable();
            $table->smallInteger('id_status_pengajuan')->nullable();

            $table->foreign('id_tujuan_permohonan')->references('id')->on('tujuan_permohonan');
            $table->foreign('id_jenis_permohonan')->references('id')->on('jenis_permohonan');
            $table->foreign('id_kondisi_lahan')->references('id')->on('kondisi_lahan');
            $table->foreign('id_masa_berlaku')->references('id')->on('masa_berlaku');
            $table->foreign('id_kepemilikan_tanah')->references('id')->on('kepemilikan_tanah');
            $table->foreign('id_kelurahan')->references('id')->on('kelurahan');
            $table->foreign('id_penggunaan')->references('id')->on('penggunaan_tanah_desa');
            $table->foreign('id_jenis_pengajuan')->references('id')->on('jenis_pengajuan');
            $table->foreign('id_status_pengajuan')->references('id')->on('status_pengajuan');
        });

        Schema::create('persil_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan_tanah_desa')->notNullable();
            $table->string('persil', 64)->nullable();
            $table->string('bidang', 20)->nullable();
            $table->string('sub_persil', 20)->nullable();

            $table->foreign('id_pengajuan_tanah_desa')->references('id')->on('pengajuan_tanah_desa');
        });

        Schema::create('verifikasi', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan')->notNullable();
            $table->unsignedInteger('id_user')->nullable();
            $table->smallInteger('status')->notNullable();
            $table->string('alasan', 225)->notNullable();

            $table->foreign('id_pengajuan')->references('id')->on('pengajuan');
            $table->foreign('id_user')->references('id')->on('users');
        });

        Schema::create('verifikasi_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan_tanah_desa')->notNullable();
            $table->unsignedInteger('id_user')->nullable();
            $table->smallInteger('status')->notNullable();
            $table->string('alasan', 225)->notNullable();

            $table->foreign('id_pengajuan_tanah_desa')->references('id')->on('pengajuan_tanah_desa');
            $table->foreign('id_user')->references('id')->on('users');
        });

        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan')->notNullable();
            $table->string('no_surat', 50)->notNullable();
            $table->string('keterangan', 225)->notNullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_pengajuan')->references('id')->on('pengajuan');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('rekomendasi_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan_tanah_desa')->notNullable();
            $table->string('no_surat', 50)->notNullable();
            $table->string('keterangan', 225)->notNullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_pengajuan_tanah_desa')->references('id')->on('pengajuan_tanah_desa');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('persetujuan_kadipaten', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan')->notNullable();
            $table->smallInteger('status')->notNullable();
            $table->string('no_surat', 50)->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('keterangan', 225)->nullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_pengajuan')->references('id')->on('pengajuan');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('persetujuan_kadipaten_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan_tanah_desa')->notNullable();
            $table->smallInteger('status')->notNullable();
            $table->string('no_surat', 50)->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('keterangan', 225)->nullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_pengajuan_tanah_desa')->references('id')->on('pengajuan_tanah_desa');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('sk_gubernur', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan')->notNullable();
            $table->string('no_sk', 50)->nullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_pengajuan')->references('id')->on('pengajuan');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('sk_gubernur_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedInteger('id_pengajuan_tanah_desa')->notNullable();
            $table->string('no_sk', 50)->nullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_pengajuan_tanah_desa')->references('id')->on('pengajuan_tanah_desa');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('lampiran_pengajuan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('id_lampiran_jenis')->notNullable();
            $table->smallInteger('id_pengajuan')->notNullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_lampiran_jenis')->references('id')->on('lampiran_jenis');
            $table->foreign('id_pengajuan')->references('id')->on('pengajuan');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('lampiran_pengajuan_tanah_desa', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('id_lampiran_jenis')->notNullable();
            $table->smallInteger('id_pengajuan_tanah_desa')->notNullable();
            $table->unsignedInteger('id_file')->nullable();

            $table->foreign('id_lampiran_jenis')->references('id')->on('lampiran_jenis');
            $table->foreign('id_pengajuan_tanah_desa')->references('id')->on('pengajuan_tanah_desa');
            $table->foreign('id_file')->references('id')->on('file');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampiran_pengajuan_tanah_desa');
        Schema::dropIfExists('lampiran_pengajuan');
        Schema::dropIfExists('sk_gubernur_tanah_desa');
        Schema::dropIfExists('sk_gubernur');
        Schema::dropIfExists('persetujuan_kadipaten_tanah_desa');
        Schema::dropIfExists('persetujuan_kadipaten');
        Schema::dropIfExists('rekomendasi_tanah_desa');
        Schema::dropIfExists('rekomendasi');
        Schema::dropIfExists('verifikasi_tanah_desa');
        Schema::dropIfExists('verifikasi');
        Schema::dropIfExists('persil_tanah_desa');
        Schema::dropIfExists('pengajuan_tanah_desa');
        Schema::dropIfExists('pengajuan');
    }
};
