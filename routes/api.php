<?php

use App\Http\Controllers\API\PetaController;
use App\Http\Controllers\API\VectorTileController;
use App\Http\Controllers\API\WilayahController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function () {

    Route::prefix('wilayah')->group(function () {
        Route::get('/provinsi',         [WilayahController::class, 'provinsi']);
        Route::get('/kabupaten',        [WilayahController::class, 'kabupaten']);
        Route::get('/kecamatan',        [WilayahController::class, 'kecamatan']);
        Route::get('/kelurahan',        [WilayahController::class, 'kelurahan']);

        Route::get('/provinsi/bbox',    [WilayahController::class, 'provinsiBbox']);
        Route::get('/kabupaten/bbox',   [WilayahController::class, 'kabupatenBbox']);
        Route::get('/kecamatan/bbox',   [WilayahController::class, 'kecamatanBbox']);
        Route::get('/kelurahan/bbox',   [WilayahController::class, 'kelurahanBbox']);

        Route::get('/provinsi/geojson',  [WilayahController::class, 'provinsiGeojson']);
        Route::get('/kabupaten/geojson', [WilayahController::class, 'kabupatenGeojson']);
        Route::get('/kecamatan/geojson', [WilayahController::class, 'kecamatanGeojson']);
        Route::get('/kelurahan/geojson', [WilayahController::class, 'kelurahanGeojson']);
    });

    Route::prefix('peta')->group(function () {
        // Endpoint GeoJSON (dipertahankan untuk kompatibilitas backward)
        Route::get('/bidang', [PetaController::class, 'bidang']);
    });

    // ── Vector Tile Endpoints (MVT / Mapbox Vector Tiles) ────────────────────
    //
    // Format: /api/v1/tiles/{layer}/{z}/{x}/{y}
    // Content-Type: application/x-protobuf
    //
    // Layer yang tersedia:
    //   bidang  – polygon bidang tanah (dengan filter kategori, jenis hak, dll.)
    //   wilayah – batas administratif (otomatis pilih level sesuai zoom)
    //
    // Query params untuk layer bidang (semua opsional):
    //   kategori_ids[]          int[]
    //   penggunaan_ids[]        int[]
    //   jenis_hak_ids[]         int[]
    //   jenis_hak_adat_ids[]    int[]
    //   status_kesesuaian_ids[] int[]
    //   all                     boolean  – load semua bidang tanpa filter
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('tiles')->group(function () {
        Route::get('/bidang/{z}/{x}/{y}',  [VectorTileController::class, 'bidang'])
            ->where(['z' => '[0-9]+', 'x' => '[0-9]+', 'y' => '[0-9]+']);

        Route::get('/wilayah/{z}/{x}/{y}', [VectorTileController::class, 'wilayah'])
            ->where(['z' => '[0-9]+', 'x' => '[0-9]+', 'y' => '[0-9]+']);
    });

});
