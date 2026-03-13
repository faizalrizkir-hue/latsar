<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DmsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ElementController;

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

    Route::middleware('admin.session')->group(function () {
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::post('/accounts/{account}/reset', [AccountController::class, 'resetPassword'])->name('accounts.reset');
        Route::post('/accounts/{account}/toggle', [AccountController::class, 'toggle'])->name('accounts.toggle');
    });
});
