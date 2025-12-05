<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta pública del formulario de encuesta
Route::get('/encuesta/{token}', [SurveyController::class, 'show'])->name('survey.show');

// Dashboard administrativo
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Gestión de Usuarios
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('index');
    Route::get('/create', [UserManagementController::class, 'create'])->name('create');
    Route::post('/', [UserManagementController::class, 'store'])->name('store');
    Route::get('/{id}', [UserManagementController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
    
    // Acciones especiales
    Route::post('/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{id}/regenerate-token', [UserManagementController::class, 'regenerateToken'])->name('regenerate-token');
    Route::get('/{id}/preview', [UserManagementController::class, 'preview'])->name('preview');
    Route::get('/{id}/qr', [UserManagementController::class, 'generateQR'])->name('qr');
    
    // Exportar
    Route::get('/export/csv', [UserManagementController::class, 'export'])->name('export');
});

// Ruta de inicio
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

