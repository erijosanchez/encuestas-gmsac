<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Survey;

class SurveyController extends Controller
{
    public function show($token)
    {
        $user = User::where('unique_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Encuesta no encontrada o inactiva'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'location' => $user->location,
                ],
                'questions' => [
                    [
                        'id' => 'experience_rating',
                        'text' => '¿Cómo calificarías tu experiencia en TRIMAX?',
                        'required' => true
                    ],
                    [
                        'id' => 'service_quality_rating',
                        'text' => '¿Cómo evaluarías la calidad de atención que recibiste?',
                        'required' => false
                    ],
                    [
                        'id' => 'response_time_rating',
                        'text' => '¿Cómo calificarías el tiempo de respuesta?',
                        'required' => false
                    ],
                    [
                        'id' => 'recommendation_rating',
                        'text' => '¿Recomendarías nuestros servicios?',
                        'required' => false
                    ]
                ],
                'ratings' => [
                    ['value' => 4, 'label' => 'Excelente', 'emoji' => '😊', 'color' => '#4CAF50'],
                    ['value' => 3, 'label' => 'Bueno', 'emoji' => '🙂', 'color' => '#2196F3'],
                    ['value' => 2, 'label' => 'Regular', 'emoji' => '😐', 'color' => '#FF9800'],
                    ['value' => 1, 'label' => 'Malo', 'emoji' => '😞', 'color' => '#F44336'],
                ]
            ]
        ]);
    }

    /**
     * Guardar encuesta
     */
    public function store(Request $request, $token)
    {
        $user = User::where('unique_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Encuesta no encontrada o inactiva'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'experience_rating' => 'required|integer|between:1,4',
            'service_quality_rating' => 'nullable|integer|between:1,4',
            'response_time_rating' => 'nullable|integer|between:1,4',
            'recommendation_rating' => 'nullable|integer|between:1,4',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $survey = Survey::create([
                'user_id' => $user->id,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'experience_rating' => $request->experience_rating,
                'service_quality_rating' => $request->service_quality_rating,
                'response_time_rating' => $request->response_time_rating,
                'recommendation_rating' => $request->recommendation_rating,
                'comments' => $request->comments,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Gracias por tu opinión! Tu encuesta ha sido enviada correctamente.',
                'data' => [
                    'survey_id' => $survey->id,
                    'rating' => $survey->rating_text,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la encuesta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todas las encuestas (Admin)
     */
    public function index(Request $request)
    {
        $query = Survey::with('user:id,name,role,location');

        // Filtros
        if ($request->has('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->has('rating')) {
            $query->byRating($request->rating);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Ordenar por más reciente
        $surveys = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $surveys
        ]);
    }

    /**
     * Obtener estadísticas generales
     */
    public function statistics(Request $request)
    {
        $query = Survey::query();

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        if ($request->has('user_id')) {
            $query->byUser($request->user_id);
        }

        $surveys = $query->get();
        $total = $surveys->count();

        if ($total === 0) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => 0,
                    'excellent' => 0,
                    'good' => 0,
                    'regular' => 0,
                    'bad' => 0,
                    'average_rating' => 0,
                ]
            ]);
        }

        $excellent = $surveys->where('experience_rating', 4)->count();
        $good = $surveys->where('experience_rating', 3)->count();
        $regular = $surveys->where('experience_rating', 2)->count();
        $bad = $surveys->where('experience_rating', 1)->count();
        $avgRating = $surveys->avg('experience_rating');

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'excellent' => $excellent,
                'good' => $good,
                'regular' => $regular,
                'bad' => $bad,
                'average_rating' => round($avgRating, 2),
                'percentage_excellent' => round(($excellent / $total) * 100, 1),
                'percentage_good' => round(($good / $total) * 100, 1),
                'percentage_regular' => round(($regular / $total) * 100, 1),
                'percentage_bad' => round(($bad / $total) * 100, 1),
                'chart_data' => [
                    'labels' => ['Excelente', 'Bueno', 'Regular', 'Malo'],
                    'values' => [$excellent, $good, $regular, $bad],
                    'colors' => ['#4CAF50', '#2196F3', '#FF9800', '#F44336'],
                ]
            ]
        ]);
    }
}
