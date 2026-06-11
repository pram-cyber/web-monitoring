<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\UserController;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return redirect('/user/dashboard');
    }
    return redirect('/login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->middleware(['auth'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/history', [AdminController::class, 'history'])->name('history');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::post('/trash-bins/{id}/mark-empty', [\App\Http\Controllers\TrashBinController::class, 'markAsEmpty'])->name('trash-bins.mark-empty');
    Route::resource('trash-bins', \App\Http\Controllers\TrashBinController::class)->names('trash-bins');
    Route::get('/sensor-logs', [AdminController::class, 'sensorLogs'])->name('sensor-logs');
    Route::delete('/sensor-logs/clear', [AdminController::class, 'clearSensorLogs'])->name('sensor-logs.clear');
    Route::delete('/sensor-logs/{id}', [AdminController::class, 'destroySensorLog'])->name('sensor-logs.destroy');
    Route::get('/trends', [AdminController::class, 'trends'])->name('trends');
    Route::get('/volume', [AdminController::class, 'volume'])->name('volume');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::post('/reports/{id}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');
    Route::delete('/reports/{id}', [AdminController::class, 'destroyReport'])->name('reports.destroy');
});

// User Routes
// User Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard');
    Route::get('/nearby', [UserController::class, 'nearby'])->name('nearby');
    Route::get('/history', [UserController::class, 'history'])->name('history');
    Route::get('/reports', [UserController::class, 'reports'])->name('reports');
    Route::post('/reports', [UserController::class, 'storeReport'])->name('reports.store');
    Route::post('/settings', [UserController::class, 'updateSettings'])->name('settings.update');
});

Route::get('/user/settings', function () {
    return redirect()->route('settings');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [\App\Http\Controllers\ProfileController::class, 'settings'])->name('settings');
    Route::delete('/settings/delete-account', [\App\Http\Controllers\ProfileController::class, 'destroyAccount'])->name('settings.destroy');
});

require __DIR__.'/auth.php';