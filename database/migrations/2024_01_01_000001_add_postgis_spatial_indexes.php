<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migrasi ini:
 * 1. Memastikan extension PostGIS aktif
 * 2. Menambahkan GIST index pada kolom geom di tabel bidang, persil, dan wilayah
 * 3. Menambahkan kolom geom pada tabel wilayah jika belum ada (nullable)
 *
 * Jalankan: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Aktifkan PostGIS jika belum aktif
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis_topology');

        // 2. Index GIST pada tabel bidang (paling kritis)
        //    Cek dulu agar tidak error jika sudah ada
        $this->createGistIndexIfNotExists('bidang', 'geom', 'idx_bidang_geom_gist');

        // 3. Index GIST pada tabel persil
        $this->createGistIndexIfNotExists('persil', 'geom', 'idx_persil_geom_gist');

        // 4. Index pada FK bidang yang sering difilter
        $this->createIndexIfNotExists('bidang', 'id_kategori',         'idx_bidang_id_kategori');
        $this->createIndexIfNotExists('bidang', 'id_penggunaan',       'idx_bidang_id_penggunaan');
        $this->createIndexIfNotExists('bidang', 'id_jenis_hak',        'idx_bidang_id_jenis_hak');
        $this->createIndexIfNotExists('bidang', 'id_jenis_hak_adat',   'idx_bidang_id_jenis_hak_adat');
        $this->createIndexIfNotExists('bidang', 'id_status_kesesuaian','idx_bidang_id_status_kesesuaian');
        $this->createIndexIfNotExists('bidang', 'id_persil',           'idx_bidang_id_persil');

        // 5. Index pada FK persil untuk filter wilayah
        $this->createIndexIfNotExists('persil', 'id_kabupaten',  'idx_persil_id_kabupaten');
        $this->createIndexIfNotExists('persil', 'id_kecamatan',  'idx_persil_id_kecamatan');
        $this->createIndexIfNotExists('persil', 'id_kelurahan',  'idx_persil_id_kelurahan');

        // 6. GIST index pada tabel wilayah jika kolom geom ada
        foreach (['provinsi', 'kabupaten', 'kecamatan', 'kelurahan'] as $table) {
            if ($this->columnExists($table, 'geom')) {
                $this->createGistIndexIfNotExists($table, 'geom', "idx_{$table}_geom_gist");
            }
        }
    }

    public function down(): void
    {
        $indexes = [
            'idx_bidang_geom_gist',
            'idx_persil_geom_gist',
            'idx_bidang_id_kategori',
            'idx_bidang_id_penggunaan',
            'idx_bidang_id_jenis_hak',
            'idx_bidang_id_jenis_hak_adat',
            'idx_bidang_id_status_kesesuaian',
            'idx_bidang_id_persil',
            'idx_persil_id_kabupaten',
            'idx_persil_id_kecamatan',
            'idx_persil_id_kelurahan',
            'idx_provinsi_geom_gist',
            'idx_kabupaten_geom_gist',
            'idx_kecamatan_geom_gist',
            'idx_kelurahan_geom_gist',
        ];

        foreach ($indexes as $index) {
            DB::statement("DROP INDEX IF EXISTS {$index}");
        }
    }

    // ──────────────────────────────────────────────────────────────────────────

    private function createGistIndexIfNotExists(string $table, string $column, string $indexName): void
    {
        if (!$this->indexExists($indexName)) {
            DB::statement("CREATE INDEX {$indexName} ON {$table} USING GIST ({$column})");
        }
    }

    private function createIndexIfNotExists(string $table, string $column, string $indexName): void
    {
        if (!$this->indexExists($indexName)) {
            DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column})");
        }
    }

    private function indexExists(string $indexName): bool
    {
        $result = DB::selectOne(
            "SELECT 1 FROM pg_indexes WHERE indexname = ?",
            [$indexName]
        );
        return $result !== null;
    }

    private function columnExists(string $table, string $column): bool
    {
        $result = DB::selectOne(
            "SELECT 1 FROM information_schema.columns WHERE table_name = ? AND column_name = ?",
            [$table, $column]
        );
        return $result !== null;
    }
};
