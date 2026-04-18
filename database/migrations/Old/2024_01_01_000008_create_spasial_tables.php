<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----- kategori_rencana_tata_ruang -----
        Schema::create('kategori_rencana_tata_ruang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 256)->nullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->notNullable()->default(0);
        });

        Schema::create('rencana_tata_ruang', function (Blueprint $table) {
            $table->increments('gid');
            $table->integer('____gid')->nullable();
            $table->integer('fid_pola_r')->nullable();
            $table->integer('fid_batas_')->nullable();
            $table->integer('id')->nullable();
            $table->integer('fid_edit_1')->nullable();
            $table->string('kws', 254)->nullable();
            $table->string('simbol', 254)->nullable();
            $table->decimal('luas')->nullable();
            $table->integer('fid_kecama')->nullable();
            $table->integer('id_1')->nullable();
            $table->string('kecamatan', 254)->nullable();
            $table->integer('id_kategori_rencana_tata_ruang')->nullable();
            $table->integer('id_kabupaten')->nullable();
            $table->geometry('geom', 'multipolygon', 4326)->nullable();

            $table->foreign('id_kategori_rencana_tata_ruang')->references('id')->on('kategori_rencana_tata_ruang');
            $table->foreign('id_kabupaten')->references('id')->on('kabupaten');
        });

        // ----- kategori_sarana_prasarana -----
        Schema::create('kategori_sarana_prasarana', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 256)->nullable();
            $table->string('warna', 15)->nullable();
            $table->smallInteger('ontop')->notNullable()->default(0);
        });

        Schema::create('sarana_prasarana', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('nama_fasum', 254)->nullable();
            $table->string('jenis', 254)->nullable();
            $table->string('kategori', 254)->nullable();
            $table->string('nama_foto', 254)->nullable();
            $table->string('link_foto', 254)->nullable();
            $table->string('keterangan', 254)->nullable();
            $table->string('id', 254)->nullable();
            $table->integer('id_kategori_sarana_prasarana')->nullable();
            $table->integer('id_kabupaten')->nullable();
            $table->geometry('geom', 'point', 4326)->nullable();

            $table->foreign('id_kategori_sarana_prasarana')->references('id')->on('kategori_sarana_prasarana');
            $table->foreign('id_kabupaten')->references('id')->on('kabupaten');
        });

        // ----- idmc_kawasan_strategis -----
        Schema::create('idmc_kawasan_strategis_jenis', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nama', 100)->nullable();
            $table->string('warna', 7)->nullable();
        });

        Schema::create('idmc_kawasan_strategis', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('kabupaten', 50)->nullable();
            $table->string('kecamatan', 50)->nullable();
            $table->string('desa', 50)->nullable();
            $table->string('koridor', 50)->nullable();
            $table->string('wp', 50)->nullable();
            $table->string('wadmkc', 50)->nullable();
            $table->string('wadmkk', 50)->nullable();
            $table->string('keterangan', 50)->nullable();
            $table->double('luas_ha')->nullable();
            $table->string('nama', 30)->nullable();
            $table->string('ket', 50)->nullable();
            $table->string('keterang_1', 50)->nullable();
            $table->string('keta', 75)->nullable();
            $table->string('wadmkc_1', 50)->nullable();
            $table->string('wadmkk_1', 50)->nullable();
            $table->string('ket_1', 50)->nullable();
            $table->geometry('geom')->nullable(); // MultiPolygonZM SRID 32749
            $table->integer('id_jenis')->nullable();

            $table->foreign('id_jenis')->references('id')->on('idmc_kawasan_strategis_jenis');
        });

        Schema::create('idmc_kawasan_strategis_kasultanan', function (Blueprint $table) {
            $table->increments('gid');
            $table->decimal('objectid', 10, 0)->nullable();
            $table->string('satuan_rua', 254)->nullable();
            $table->geometry('geom')->nullable(); // Point SRID 32749
        });

        // ----- idmc_pola_ruang -----
        Schema::create('idmc_pola_ruang_jenis', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nama', 100)->nullable();
            $table->string('warna', 7)->nullable();
        });

        Schema::create('idmc_pola_ruang', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('pola_iv', 50)->nullable();
            $table->string('pola_iii', 50)->nullable();
            $table->string('pola_ii', 50)->nullable();
            $table->string('pola_i', 50)->nullable();
            $table->string('nama_kwsn', 50)->nullable();
            $table->string('wadmkc', 50)->nullable();
            $table->string('wadmkk', 50)->nullable();
            $table->string('wadmpr', 50)->nullable();
            $table->string('kecamata_1', 50)->nullable();
            $table->string('kabupaten', 30)->nullable();
            $table->string('wilayah', 50)->nullable();
            $table->string('ket_kra', 50)->nullable();
            $table->string('sumber_kra', 50)->nullable();
            $table->string('nama_kbak', 50)->nullable();
            $table->geometry('geom')->nullable(); // MultiPolygonZM SRID 32749
            $table->integer('id_jenis')->nullable();

            $table->foreign('id_jenis')->references('id')->on('idmc_pola_ruang_jenis');
        });

        // ----- idmc_struktur_ruang -----
        Schema::create('idmc_struktur_ruang_jenis', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nama', 100)->nullable();
            $table->string('warna', 7)->nullable();
        });

        Schema::create('idmc_struktur_ruang', function (Blueprint $table) {
            $table->increments('gid');
            $table->decimal('objectid', 10, 0)->nullable();
            $table->string('ket_resapa', 50)->nullable();
            $table->string('smbr_resap', 50)->nullable();
            $table->string('wa_1', 40)->nullable();
            $table->string('ta_1', 15)->nullable();
            $table->string('kabupaten', 15)->nullable();
            $table->string('kewenangan', 15)->nullable();
            $table->string('nama', 50)->nullable();
            $table->string('sumber_cat', 30)->nullable();
            $table->string('nama_cat', 30)->nullable();
            $table->string('ket_cat', 30)->nullable();
            $table->string('wadmkc', 50)->nullable();
            $table->string('wadmkk', 50)->nullable();
            $table->string('nama_das_1', 50)->nullable();
            $table->string('sumber_das', 50)->nullable();
            $table->string('nama_waduk', 30)->nullable();
            $table->string('ket_waduk', 20)->nullable();
            $table->decimal('shape_leng')->nullable();
            $table->decimal('shape_area')->nullable();
            $table->geometry('geom')->nullable(); // MultiPolygonZM SRID 32749
            $table->integer('id_jenis')->nullable();

            $table->foreign('id_jenis')->references('id')->on('idmc_struktur_ruang_jenis');
        });

        Schema::create('idmc_struktur_ruang_jaringan', function (Blueprint $table) {
            $table->increments('gid');
            $table->decimal('objectid', 10, 0)->nullable();
            $table->string('nama', 50)->nullable();
            $table->string('keterangan', 50)->nullable();
            $table->string('ruas_jalur', 50)->nullable();
            $table->decimal('panjang_km')->nullable();
            $table->string('kewenangan', 50)->nullable();
            $table->string('fungsi', 50)->nullable();
            $table->string('rencana', 50)->nullable();
            $table->string('air', 50)->nullable();
            $table->string('jenis', 50)->nullable();
            $table->string('sumber', 50)->nullable();
            $table->string('kondisi', 50)->nullable();
            $table->string('handle', 16)->nullable();
            $table->geometry('geom')->nullable(); // MultiLineStringZM SRID 32749
        });

        Schema::create('idmc_struktur_ruang_point', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('nama', 50)->nullable();
            $table->string('lokasi', 50)->nullable();
            $table->string('keterangan', 50)->nullable();
            $table->string('jenis', 50)->nullable();
            $table->string('hirarki', 50)->nullable();
            $table->string('kondisi', 50)->nullable();
            $table->string('status_1', 50)->nullable();
            $table->geometry('geom')->nullable(); // PointZM SRID 32749
        });

        // ----- RDTR per wilayah -----
        Schema::create('rdtr_bantul_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nama', 100)->notNullable();
            $table->char('warna', 7)->notNullable();
        });

        Schema::create('rdtr_bantul', function (Blueprint $table) {
            $table->increments('gid');
            $table->decimal('objectid', 10, 0)->nullable();
            $table->string('pola_ruang', 50)->nullable();
            $table->decimal('ha')->nullable();
            $table->string('desa', 30)->nullable();
            $table->string('kecamatan', 15)->nullable();
            $table->geometry('geom', 'multipolygon', 32749)->nullable();
            $table->integer('id_kawasan')->nullable();

            $table->foreign('id_kawasan')->references('id')->on('rdtr_bantul_kawasan');
        });

        Schema::create('rdtr_diy', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('keterangan', 50)->nullable();
            $table->string('peruntukan', 100)->nullable();
            $table->geometry('geom')->nullable(); // MultiPolygonZM SRID 32749
        });

        Schema::create('rdtr_kota_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nama', 100)->notNullable();
            $table->char('warna', 7)->notNullable();
        });

        Schema::create('rdtr_kota', function (Blueprint $table) {
            $table->increments('gid');
            $table->decimal('symbolid', 10, 0)->nullable();
            $table->string('sub_zona', 50)->nullable();
            $table->geometry('geom')->nullable(); // MultiPolygonZM SRID 32749
            $table->integer('id_kawasan')->nullable();

            $table->foreign('id_kawasan')->references('id')->on('rdtr_kota_kawasan');
        });

        Schema::create('rdtr_sleman_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nama', 100)->notNullable();
            $table->char('warna', 7)->notNullable();
        });

        Schema::create('rdtr_sleman', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('kecamatan', 50)->nullable();
            $table->string('peruntukan', 75)->nullable();
            $table->geometry('geom', 'multipolygon', 32749)->nullable();
            $table->integer('id_kawasan')->nullable();

            $table->foreign('id_kawasan')->references('id')->on('rdtr_sleman_kawasan');
        });

        // ----- RTRW & polaruang -----
        Schema::create('rtrw_diy_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->char('warna', 7)->nullable();
            $table->string('nama', 32)->nullable();
        });

        Schema::create('rtrw_diy', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('pola_iv', 50)->nullable();
            $table->string('pola_iii', 50)->nullable();
            $table->string('pola_ii', 50)->nullable();
            $table->string('pola_i', 50)->nullable();
            $table->double('luas_ha')->nullable();
            $table->string('nama_kwsn', 50)->nullable();
            $table->geometry('geom')->nullable(); // MultiPolygonZM SRID 32749
            $table->integer('id_kawasan')->nullable();

            $table->foreign('id_kawasan')->references('id')->on('rtrw_diy_kawasan');
        });

        Schema::create('polaruang_bantul_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->char('warna', 7)->nullable();
            $table->string('nama', 32)->nullable();
        });

        Schema::create('polaruang_bantul', function (Blueprint $table) {
            $table->increments('gid');
            $table->integer('fid_polaru')->nullable();
            $table->double('id')->nullable();
            $table->string('kawasan', 50)->nullable();
            $table->string('guna_lahan', 50)->nullable();
            $table->integer('fid_insteb')->nullable();
            $table->string('provinsi', 30)->nullable();
            $table->string('kecamatan', 18)->nullable();
            $table->integer('kode_kec')->nullable();
            $table->decimal('shape_le_1')->nullable();
            $table->decimal('shape_area')->nullable();
            $table->string('keterangan', 50)->nullable();
            $table->geometry('geom', 'multipolygon', 32749)->nullable();
            $table->integer('id_kawasan')->nullable();
            $table->string('nama', 32)->nullable();

            $table->foreign('id_kawasan')->references('id')->on('polaruang_bantul_kawasan');
        });

        Schema::create('polaruang_gk_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->char('warna', 7)->nullable();
            $table->string('nama', 32)->nullable();
        });

        Schema::create('polaruang_gk', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('pola_ruang', 254)->nullable();
            $table->geometry('geom', 'multipolygon', 32749)->nullable();
            $table->integer('id_kawasan')->nullable();

            $table->foreign('id_kawasan')->references('id')->on('polaruang_gk_kawasan');
        });

        Schema::create('polaruang_kp_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->char('warna', 7)->nullable();
            $table->string('nama', 32)->nullable();
        });

        Schema::create('polaruang_kp', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('pola_ruang', 254)->nullable();
            $table->string('fungsi', 200)->nullable();
            $table->string('k_budidaya', 200)->nullable();
            $table->string('k_lindung', 200)->nullable();
            $table->string('kws_genera', 50)->nullable();
            $table->geometry('geom', 'multipolygon', 32749)->nullable();
            $table->integer('id_kawasan')->nullable();

            $table->foreign('id_kawasan')->references('id')->on('polaruang_kp_kawasan');
        });

        Schema::create('polaruang_sleman_kawasan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->char('warna', 7)->nullable();
            $table->string('nama', 32)->nullable();
        });

        Schema::create('polaruang_sleman', function (Blueprint $table) {
            $table->increments('gid');
            $table->string('keterangan', 40)->nullable();
            $table->geometry('geom', 'multipolygon', 32749)->nullable();
            $table->integer('id_kawasan')->nullable();

            $table->foreign('id_kawasan')->references('id')->on('polaruang_sleman_kawasan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polaruang_sleman');
        Schema::dropIfExists('polaruang_sleman_kawasan');
        Schema::dropIfExists('polaruang_kp');
        Schema::dropIfExists('polaruang_kp_kawasan');
        Schema::dropIfExists('polaruang_gk');
        Schema::dropIfExists('polaruang_gk_kawasan');
        Schema::dropIfExists('polaruang_bantul');
        Schema::dropIfExists('polaruang_bantul_kawasan');
        Schema::dropIfExists('rtrw_diy');
        Schema::dropIfExists('rtrw_diy_kawasan');
        Schema::dropIfExists('rdtr_sleman');
        Schema::dropIfExists('rdtr_sleman_kawasan');
        Schema::dropIfExists('rdtr_kota');
        Schema::dropIfExists('rdtr_kota_kawasan');
        Schema::dropIfExists('rdtr_diy');
        Schema::dropIfExists('rdtr_bantul');
        Schema::dropIfExists('rdtr_bantul_kawasan');
        Schema::dropIfExists('idmc_struktur_ruang_point');
        Schema::dropIfExists('idmc_struktur_ruang_jaringan');
        Schema::dropIfExists('idmc_struktur_ruang');
        Schema::dropIfExists('idmc_struktur_ruang_jenis');
        Schema::dropIfExists('idmc_pola_ruang');
        Schema::dropIfExists('idmc_pola_ruang_jenis');
        Schema::dropIfExists('idmc_kawasan_strategis_kasultanan');
        Schema::dropIfExists('idmc_kawasan_strategis');
        Schema::dropIfExists('idmc_kawasan_strategis_jenis');
        Schema::dropIfExists('sarana_prasarana');
        Schema::dropIfExists('kategori_sarana_prasarana');
        Schema::dropIfExists('rencana_tata_ruang');
        Schema::dropIfExists('kategori_rencana_tata_ruang');
    }
};
