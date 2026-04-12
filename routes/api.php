<?php

use App\Http\Controllers\API\PetaController;
use App\Http\Controllers\API\WilayahController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function () {

    Route::prefix('wilayah')->group(function () {
        Route::get('/provinsi',         [WilayahController::class, 'provinsi']);
        Route::get('/kabupaten',        [WilayahController::class, 'kabupaten']);
        Route::get('/kecamatan',        [WilayahController::class, 'kecamatan']);
        Route::get('/kelurahan',        [WilayahController::class, 'kelurahan']);

        Route::get('/kabupaten/bbox',   [WilayahController::class, 'kabupatenBbox']);
        Route::get('/kecamatan/bbox',   [WilayahController::class, 'kecamatanBbox']);
        Route::get('/kelurahan/bbox',   [WilayahController::class, 'kelurahanBbox']);

        Route::get('/kabupaten/geojson', [WilayahController::class, 'kabupatenGeojson']);
        Route::get('/kecamatan/geojson', [WilayahController::class, 'kecamatanGeojson']);
        Route::get('/kelurahan/geojson', [WilayahController::class, 'kelurahanGeojson']);
    });

    Route::prefix('peta')->group(function () {
        Route::get('/bidang', [PetaController::class, 'bidang']);
    });

});
