<?php

use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Uploads (imagens e documentos)
    Route::get('/imagens', [UploadController::class, 'index'])->defaults('type', 'images')->name('uploads.images');
    Route::get('/documentos', [UploadController::class, 'index'])->defaults('type', 'documents')->name('uploads.documents');
    Route::get('/uploads/nome', [UploadController::class, 'suggest'])->name('uploads.suggest');
    Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store');
    Route::delete('/uploads/{upload}', [UploadController::class, 'destroy'])->name('uploads.destroy');

    // Área exclusiva do admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/utilizadores', [UserController::class, 'index'])->name('users');
        Route::post('/utilizadores', [UserController::class, 'store'])->name('users.store');
        Route::put('/utilizadores/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/utilizadores/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/definicoes', [SettingController::class, 'index'])->name('settings');
        Route::put('/definicoes', [SettingController::class, 'update'])->name('settings.update');

        Route::post('/categorias', [SettingController::class, 'storeCategory'])->name('categories.store');
        Route::delete('/categorias/{category}', [SettingController::class, 'destroyCategory'])->name('categories.destroy');
    });
});
