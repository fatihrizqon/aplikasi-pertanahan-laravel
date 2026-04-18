<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 256)->nullable();
        });

        // Belum Teridentifikasi, Hak Guna Bangunan, Hak Pakai, Belum Bersertifikat, Hak Milik
        Schema::create('jenis_hak', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('warna')->nullable();
            $table->timestamps();
        });

        // Belum Teridentifikasi, Keprabon, Magersari, Ngidung, Anganggo, Anggaduh, Palilah, Hak Khusus
        Schema::create('jenis_hak_adat', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('warna')->nullable();
            $table->string('legacy_id', 64)->nullable()->unique();
            $table->timestamps();
        });

        /* NEW
        Schema::create('persil', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('jenis', ['sg', 'pag', 'tk'])->nullable();
            $table->foreign('id_pemilik')->references('id')->on('pemilik');
            $table->string('nomor_persil', 128)->nullable();
            $table->string('klas', 128)->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->string('alamat')->nullable();
            $table->foreign('kode_kelurahan')->references('kode')->on('kelurahan');
            $table->foreign('kode_kecamatan')->references('kode')->on('kecamatan');
            $table->foreign('kode_kabupaten')->references('kode')->on('kabupaten');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');
            $table->string('batas_utara', 256)->nullable();
            $table->string('batas_selatan', 256)->nullable();
            $table->string('batas_timur', 256)->nullable();
            $table->string('batas_barat', 256)->nullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
            $table->string('koordinat')->nullable();
            $table->string('legacy_id', 64)->nullable()->unique();
            $table->timestamps();
        });
        */

        Schema::create('persil', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('id_kategori')->nullable();
            $table->smallInteger('id_kelurahan')->nullable();
            $table->string('jalan', 128)->nullable();
            $table->string('no_persil', 32)->notNullable()->unique();
            $table->string('no_sertifikat', 64)->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->string('batas_utara', 256)->nullable();
            $table->string('batas_selatan', 256)->nullable();
            $table->string('batas_timur', 256)->nullable();
            $table->string('batas_barat', 256)->nullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
            $table->string('no_surat_ukur', 16)->nullable();
            $table->smallInteger('id_kategori_tanah_desa')->nullable();
            $table->string('last_updated', 500)->nullable();
            $table->smallInteger('status_verifikasi')->nullable();
            $table->unsignedInteger('id_user_verifikasi')->nullable();
            $table->smallInteger('id_kategori_tanah_desa_detail')->nullable();
            $table->foreign('id_kategori')->references('id')->on('kategori');
            $table->foreign('id_kelurahan')->references('id')->on('kelurahan');
            $table->foreign('id_kategori_tanah_desa')->references('id')->on('kategori_tanah_desa');
            $table->foreign('id_kategori_tanah_desa_detail')->references('id')->on('kategori_tanah_desa_detail');
            $table->foreign('id_user_verifikasi')->references('id')->on('users');
        });

        /* NEW
        Schema::create('bidang', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('id_persil')->references('id')->on('persil');
            $table->foreign('id_pengelola')->references('id')->on('pengelola');
            $table->foreign('id_jenis_hak')->references('id')->on('jenis_hak');
            $table->foreign('id_jenis_hak_adat')->references('id')->on('jenis_hak_adat');
            $table->foreign('id_penggunaan')->references('id')->on('penggunaan');
            $table->string('nomor_hak')->nullable();
            $table->string('nomor_hak_adat')->nullable();
            $table->string('nomor_bidang')->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->enum('status_sertipikat', ['belum teridentifikasi', 'belum bersertipikat', 'bersertipikat'])->nullable();
            $table->enum('status_kesesuaian', ['belum teridentifikasi', 'sesuai', 'tidak sesuai', 'sesuai dengan syarat'])->nullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();
            $table->foreign('id_file')->references('id')->on('file')->nullable();
            $table->string('keterangan')->nullable();
        });
        */

        Schema::create('bidang', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('id_jenis_hak')->nullable(); // -> id_jenis_hak_adat
            $table->smallInteger('id_jenis_uupa')->nullable(); // -> id_jenis_hak
            $table->string('no_surat_uupa', 64)->nullable(); // -> nomor_hak
            $table->string('no_bidang', 64)->notNullable(); // -> nomor_bidang
            $table->smallInteger('id_pengelola')->nullable(); // ok
            $table->string('no_kekancingan', 64)->nullable(); // -> nomor_hak_adat
            $table->decimal('luas', 10, 2)->nullable(); // -> ok
            $table->smallInteger('id_penggunaan')->nullable(); // convert penggunaan_rtr to penggunaan
            $table->date('tgl_mulai')->nullable(); // drop
            $table->date('tgl_selesai')->nullable(); // drop
            $table->string('keterangan', 512)->nullable();
            $table->smallInteger('id_status_kesesuaian')->nullable(); // -> status_kesesuaian
            $table->string('no_sertifikat', 128)->nullable(); // convert to nomor_hak
            $table->unsignedInteger('id_file')->nullable();
            $table->smallInteger('id_status_sertifikat')->nullable(); // merge with id_jenis_hak
            $table->geometry('geom', 'multipolygon', 4326)->nullable(); // ok
            $table->unsignedInteger('id_persil')->nullable(); // ok
            $table->smallInteger('id_kesesuaian_rdtr')->nullable(); // merge with id_status_kesesuaian
            $table->unsignedInteger('id_peta')->nullable(); // merge to id_file
            $table->string('id_sg_pag_lama', 256)->nullable(); // possibly dropped
            $table->string('last_updated', 500)->nullable();

            $table->foreign('id_jenis_hak')->references('id')->on('jenis_hak');
            $table->foreign('id_jenis_uupa')->references('id')->on('jenis_uupa');
            $table->foreign('id_pengelola')->references('id')->on('pengelola');
            $table->foreign('id_penggunaan')->references('id')->on('penggunaan_rtr');
            $table->foreign('id_status_kesesuaian')->references('id')->on('status_kesesuaian');
            $table->foreign('id_status_sertifikat')->references('id')->on('status_sertifikat');
            $table->foreign('id_file')->references('id')->on('file');
            $table->foreign('id_persil')->references('id')->on('persil');
            $table->foreign('id_kesesuaian_rdtr')->references('id')->on('status_kesesuaian');
            $table->foreign('id_peta')->references('id')->on('file');
        });

        Schema::create('sub_persil', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_bidang')->notNullable();
            $table->string('no_sub_persil', 64)->nullable();
            $table->string('no_serat_kekancingan', 64)->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->decimal('luas', 10, 2)->nullable();
            $table->smallInteger('id_penggunaan')->nullable();
            $table->smallInteger('id_pengelola')->nullable();
            $table->string('keterangan', 512)->nullable();
            $table->unsignedInteger('id_file')->nullable();
            $table->string('last_updated', 500)->nullable();

            $table->foreign('id_bidang')->references('id')->on('bidang');
            $table->foreign('id_penggunaan')->references('id')->on('penggunaan_rtr');
            $table->foreign('id_pengelola')->references('id')->on('pengelola');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('galeri_bidang', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_bidang')->nullable();
            $table->unsignedInteger('id_file')->nullable();
            $table->string('nama', 128)->nullable();

            $table->foreign('id_bidang')->references('id')->on('bidang');
            $table->foreign('id_file')->references('id')->on('file');
        });

        Schema::create('galeri_sub_persil', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_file')->nullable();
            $table->unsignedInteger('id_sub_persil')->nullable();
            $table->string('nama', 255)->nullable();

            $table->foreign('id_file')->references('id')->on('file');
            $table->foreign('id_sub_persil')->references('id')->on('sub_persil');
        });

        Schema::create('monitoring', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_jenis_monitoring')->nullable();
            $table->text('hasil')->nullable();
            $table->unsignedInteger('id_file')->nullable();
            $table->unsignedInteger('id_file_pendukung')->nullable();
            $table->date('tanggal')->nullable();
            $table->unsignedInteger('id_persil')->nullable();

            $table->foreign('id_file')->references('id')->on('file');
            $table->foreign('id_file_pendukung')->references('id')->on('file');
            $table->foreign('id_persil')->references('id')->on('persil');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring');
        Schema::dropIfExists('galeri_sub_persil');
        Schema::dropIfExists('galeri_bidang');
        Schema::dropIfExists('sub_persil');
        Schema::dropIfExists('bidang');
        Schema::dropIfExists('persil');
        Schema::dropIfExists('file');
    }
};
