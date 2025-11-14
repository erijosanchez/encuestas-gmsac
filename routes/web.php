<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardWebController;
use App\Http\Controllers\Web\UsersWebController;
use App\Http\Controllers\SurveyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta raíz redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación (públicas)
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Encuesta pública (sin autenticación)
Route::get('/encuesta/{token}', function($token) {
    return view('survey.form', compact('token'));
})->name('survey.show');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard -> links del sidebar
    Route::get('/dashboard', [DashboardWebController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/detalles-zona', [DashboardWebController::class, 'detallezona' ])->name('dashboard.detallezona');
    Route::get('/dashboard/alertas', [DashboardWebController::class, 'alertas'])->name('dashboard.alertas');
    Route::get('/dashboard/tendencias', [DashboardWebController::class, 'tendencias'])->name('dashboar.tendenc');
    Route::get('/dashboard/reconocimientos', [DashboardWebController::class, 'reconocimientos'])->name('dashboard.reconocimientos');
    
    // Gestión de usuarios
    Route::resource('users', UsersWebController::class);
    Route::post('/users/{id}/regenerate-token', [UsersWebController::class, 'regenerateToken'])->name('users.regenerate-token');
    
});