<?php

use App\Enum\RolesEnum;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\Referensi\JenisHakController;
use App\Http\Controllers\Dashboard\Referensi\PengelolaController;
use App\Http\Controllers\Dashboard\Referensi\PenggunaanRDTRController;
use App\Http\Controllers\Dashboard\Referensi\PenggunaanTKDController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::middleware('auth')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [DashboardController::class, 'overview'])->name('dashboard.overview');
        Route::get('/monitoring', [DashboardController::class, 'monitoring'])->name('dashboard.monitoring');

        Route::resource('jenis_hak', JenisHakController::class);
        Route::resource('pengelola', PengelolaController::class);
        Route::resource('penggunaan_rdtr', PenggunaanRDTRController::class);
        Route::resource('penggunaan_tkd', PenggunaanTKDController::class);

        Route::resource('users', UserController::class);
        Route::put('users/{user}/lock', [UserController::class, 'lock'])->name('users.lock');
        Route::put('users/{user}/unlock', [UserController::class, 'unlock'])->name('users.unlock');
        Route::get('export/users', [UserController::class, 'export'])->name('users.export');
        Route::post('import/users', [UserController::class, 'import'])->name('users.import');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

});

Route::post('/notifications/{id}/mark-read', function (Request $request, $id) {
    $user = $request->user();
    $model = $user->unreadNotifications()->find($id);

    if ($model) $model->markAsRead();

    return response()->json(['status' => 'ok']);
})->name('notifications.mark-read');

Route::post('/notifications/mark-all-read', function (Request $request) {
    $user = $request->user();
    $user->unreadNotifications->markAsRead();

    return response()->json(['status' => 'ok']);
})->name('notifications.mark-all-read');


require __DIR__.'/auth.php';
require __DIR__.'/api.php';
