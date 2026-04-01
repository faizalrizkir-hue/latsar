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
use App\Http\Controllers\NotificationController;

// In Codespaces / PHP built-in server, static files can sometimes be routed into Laravel.
// These routes ensure public assets are still served correctly.
$servePublicFile = static function (string $baseDir, string $path) {
    $relativePath = trim(str_replace('\\', '/', $path), '/');
    if ($relativePath === '' || str_contains($relativePath, "\0")) {
        abort(404);
    }

    $basePath = realpath(public_path($baseDir));
    $resolvedPath = realpath(public_path(trim($baseDir.'/'.$relativePath, '/')));
    if ($basePath === false || $resolvedPath === false || !is_file($resolvedPath)) {
        abort(404);
    }

    $basePathNormalized = rtrim(str_replace('\\', '/', $basePath), '/').'/';
    $resolvedPathNormalized = str_replace('\\', '/', $resolvedPath);
    if (!str_starts_with($resolvedPathNormalized, $basePathNormalized)) {
        abort(404);
    }

    return response()->file($resolvedPath);
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

Route::middleware(['auth.session', 'db.lock'])->group(function () {
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
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::post('/notifications/auth', [NotificationController::class, 'authorizeChannel'])->name('notifications.auth');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');

    Route::middleware('admin.session')->group(function () {
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::post('/accounts/{account}/reset', [AccountController::class, 'resetPassword'])->name('accounts.reset');
        Route::post('/accounts/{account}/toggle', [AccountController::class, 'toggle'])->name('accounts.toggle');
        Route::get('/element-preferences', [ElementPreferenceController::class, 'index'])->name('element-preferences.index');
        Route::post('/element-preferences', [ElementPreferenceController::class, 'update'])->name('element-preferences.update');
        Route::post('/element-preferences/reset', [ElementPreferenceController::class, 'reset'])->name('element-preferences.reset');
        Route::post('/element-preferences/reset-data', [ElementPreferenceController::class, 'resetData'])->name('element-preferences.reset-data');
        Route::post('/element-preferences/archive-progress', [ElementPreferenceController::class, 'archiveProgress'])->name('element-preferences.archive-progress');
        Route::post('/element-preferences/load-archive', [ElementPreferenceController::class, 'loadArchive'])->name('element-preferences.load-archive');
        Route::post('/informasi-umum', [GeneralInformationController::class, 'update'])->name('informasi-umum.update');
    });
});
