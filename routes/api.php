<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas - Encuestas
Route::prefix('encuesta')->group(function () {
    // Obtener formulario de encuesta por token
    Route::get('/{token}', [SurveyController::class, 'show']);
    
    // Enviar encuesta
    Route::post('/{token}', [SurveyController::class, 'store']);
});

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});

// Rutas protegidas - Admin Panel
Route::middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/compare', [DashboardController::class, 'compare']);
        Route::get('/export', [DashboardController::class, 'export']);
    });
    
    // Gestión de usuarios (Consultores y Sedes)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/consultores', [UserController::class, 'getConsultores']); // Listar consultores disponibles
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/{id}/regenerate-token', [UserController::class, 'regenerateToken']);
        Route::get('/{id}/statistics', [UserController::class, 'statistics']);
    });
    
    // Encuestas - Admin
    Route::prefix('surveys')->group(function () {
        Route::get('/', [SurveyController::class, 'index']);
        Route::get('/statistics', [SurveyController::class, 'statistics']);
    });
});

// Ruta de prueba
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'TRIMAX Encuestas API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toDateTimeString(),
    ]);
});
