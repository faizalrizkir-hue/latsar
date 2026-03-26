<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DmsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\ElementPreferenceController;
use App\Http\Controllers\AoiController;
use App\Http\Controllers\GeneralInformationController;

// In Codespaces / PHP built-in server, static files can sometimes be routed into Laravel.
// These routes ensure public assets are still served correctly.
$servePublicFile = static function (string $baseDir, string $path) {
    $relativePath = trim(str_replace('\\', '/', $path), '/');
    $fullPath = public_path(trim($baseDir.'/'.$relativePath, '/'));

    abort_unless(is_file($fullPath), 404);

    return response()->file($fullPath);
};

Route::get('/css/{path}', fn (string $path) => $servePublicFile('css', $path))
    ->where('path', '.*');
Route::get('/js/{path}', fn (string $path) => $servePublicFile('js', $path))
    ->where('path', '.*');
Route::get('/static/{path}', fn (string $path) => $servePublicFile('static', $path))
    ->where('path', '.*');
Route::get('/build/{path}', fn (string $path) => $servePublicFile('build', $path))
    ->where('path', '.*');
Route::get('/uploads/{path}', fn (string $path) => $servePublicFile('uploads', $path))
    ->where('path', '.*');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');

Route::middleware('auth.session')->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dms', [DmsController::class, 'index'])->name('dms.index');
    Route::get('/dms/create', [DmsController::class, 'create'])->name('dms.create');
    Route::post('/dms', [DmsController::class, 'store'])->name('dms.store');
    Route::get('/dms/{id}/edit', [DmsController::class, 'edit'])->name('dms.edit');
    Route::put('/dms/{id}', [DmsController::class, 'update'])->name('dms.update');
    Route::post('/dms/{id}/archive', [DmsController::class, 'archive'])->name('dms.archive');
    Route::post('/dms/{id}/unarchive', [DmsController::class, 'unarchive'])->name('dms.unarchive');
    Route::delete('/dms/{id}', [DmsController::class, 'destroy'])->name('dms.destroy');
    Route::post('/dms/{id}/restore', [DmsController::class, 'restore'])->name('dms.restore');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/elements', [ElementController::class, 'index'])->name('elements.index');
    Route::get('/elements/{slug}', [ElementController::class, 'show'])->name('elements.show');
    Route::post('/elements/{slug}', [ElementController::class, 'store'])->name('elements.store');
    Route::get('/area-of-improvement', [AoiController::class, 'index'])->name('aoi.index');
    Route::get('/informasi-umum', [GeneralInformationController::class, 'index'])->name('informasi-umum.index');

    Route::middleware('admin.session')->group(function () {
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::post('/accounts/{account}/reset', [AccountController::class, 'resetPassword'])->name('accounts.reset');
        Route::post('/accounts/{account}/toggle', [AccountController::class, 'toggle'])->name('accounts.toggle');
        Route::get('/element-preferences', [ElementPreferenceController::class, 'index'])->name('element-preferences.index');
        Route::post('/element-preferences', [ElementPreferenceController::class, 'update'])->name('element-preferences.update');
        Route::post('/element-preferences/reset', [ElementPreferenceController::class, 'reset'])->name('element-preferences.reset');
        Route::post('/element-preferences/reset-data', [ElementPreferenceController::class, 'resetData'])->name('element-preferences.reset-data');
        Route::post('/informasi-umum', [GeneralInformationController::class, 'update'])->name('informasi-umum.update');
    });
});
