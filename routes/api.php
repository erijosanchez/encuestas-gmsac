<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Encuestas públicas
Route::get('/encuesta/{token}', [SurveyController::class, 'getData']);
Route::post('/encuesta/{token}', [SurveyController::class, 'store']);

// Estadísticas (proteger en producción)
Route::get('/stats', [DashboardController::class, 'getStats']);
