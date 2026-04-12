<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('nama', 32)->notNullable();
            $table->string('isi', 64)->notNullable();
        });

        Schema::create('kontak', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal')->nullable();
            $table->string('nama', 128)->notNullable();
            $table->string('email', 32)->notNullable();
            $table->string('subyek', 32)->notNullable();
            $table->string('pesan', 255)->nullable();
            $table->string('balasan', 255)->nullable();
            $table->smallInteger('status')->default(0);
        });

        Schema::create('tanggal_notifikasi', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal')->nullable();
            $table->string('keterangan', 25)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanggal_notifikasi');
        Schema::dropIfExists('kontak');
        Schema::dropIfExists('config');
    }
};
