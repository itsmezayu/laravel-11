<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route untuk Halaman Utama (Publik)
Route::view('/', 'welcome')->name('welcome');

// Kelompok route yang memerlukan autentikasi dan verifikasi email
Route::middleware(['auth', 'verified'])->group(function () {

    // Route untuk Dashboard utama (menerima GET dan POST)
    Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route untuk Halaman About Us (hanya admin)
    Route::get('/about-us', [AboutController::class, 'index'])->name('about.index');
    // Route Resource untuk semua fungsi CRUD Member
    Route::resource('members', MemberController::class);
    // Route untuk Halaman Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // --- KELOMPOK ROUTE KHUSUS ADMIN ---
    Route::middleware(['auth', 'can:is-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
    });
});

// Route untuk file autentikasi dari Breeze (login, register, dll)
require __DIR__ . '/auth.php';
