<?php

namespace App\Http\Controllers;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas (por defecto último mes)
        $startDate = $request->get('start_date', Carbon::now()->subMonth());
        $endDate = $request->get('end_date', Carbon::now());

        // Totales generales
        $totalSurveys = Survey::dateRange($startDate, $endDate)->count();
        $totalConsultores = User::where('role', 'consultor')->where('is_active', true)->count();
        $totalSedes = User::where('role', 'sede')->where('is_active', true)->count();

        // Distribución de calificaciones
        $surveys = Survey::dateRange($startDate, $endDate)->get();
        $excellent = $surveys->where('experience_rating', 4)->count();
        $good = $surveys->where('experience_rating', 3)->count();
        $regular = $surveys->where('experience_rating', 2)->count();
        $bad = $surveys->where('experience_rating', 1)->count();

        $avgRating = $totalSurveys > 0 ? $surveys->avg('experience_rating') : 0;

        // Encuestas por día (últimos 30 días)
        $surveysPerDay = Survey::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->dateRange($startDate, $endDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Top 5 consultores con mejor calificación
        $topConsultores = User::where('role', 'consultor')
            ->where('is_active', true)
            ->withCount(['surveys' => function ($query) use ($startDate, $endDate) {
                $query->dateRange($startDate, $endDate);
            }])
            ->with(['surveys' => function ($query) use ($startDate, $endDate) {
                $query->dateRange($startDate, $endDate);
            }])
            ->get()
            ->map(function ($user) {
                $stats = $user->getStatistics();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total_surveys' => $stats['total'],
                    'average_rating' => $stats['average_rating'],
                    'excellent' => $stats['excellent'],
                    'good' => $stats['good'],
                    'regular' => $stats['regular'],
                    'bad' => $stats['bad'],
                ];
            })
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

        // Top 5 sedes con mejor calificación
        $topSedes = User::where('role', 'sede')
            ->where('is_active', true)
            ->withCount(['surveys' => function ($query) use ($startDate, $endDate) {
                $query->dateRange($startDate, $endDate);
            }])
            ->with(['surveys' => function ($query) use ($startDate, $endDate) {
                $query->dateRange($startDate, $endDate);
            }])
            ->get()
            ->map(function ($user) {
                $stats = $user->getStatistics();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'location' => $user->location,
                    'total_surveys' => $stats['total'],
                    'average_rating' => $stats['average_rating'],
                    'excellent' => $stats['excellent'],
                    'good' => $stats['good'],
                    'regular' => $stats['regular'],
                    'bad' => $stats['bad'],
                ];
            })
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

        // Encuestas recientes
        $recentSurveys = Survey::with('user:id,name,role,location')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($survey) {
                return [
                    'id' => $survey->id,
                    'user_name' => $survey->user->name,
                    'user_role' => $survey->user->role,
                    'rating' => $survey->experience_rating,
                    'rating_text' => $survey->rating_text,
                    'client_name' => $survey->client_name,
                    'comments' => $survey->comments,
                    'created_at' => $survey->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_surveys' => $totalSurveys,
                    'total_consultores' => $totalConsultores,
                    'total_sedes' => $totalSedes,
                    'average_rating' => round($avgRating, 2),
                ],
                'ratings_distribution' => [
                    'excellent' => $excellent,
                    'good' => $good,
                    'regular' => $regular,
                    'bad' => $bad,
                    'chart' => [
                        'labels' => ['Excelente', 'Bueno', 'Regular', 'Malo'],
                        'values' => [$excellent, $good, $regular, $bad],
                        'colors' => ['#4CAF50', '#2196F3', '#FF9800', '#F44336'],
                        'percentages' => [
                            'excellent' => $totalSurveys > 0 ? round(($excellent / $totalSurveys) * 100, 1) : 0,
                            'good' => $totalSurveys > 0 ? round(($good / $totalSurveys) * 100, 1) : 0,
                            'regular' => $totalSurveys > 0 ? round(($regular / $totalSurveys) * 100, 1) : 0,
                            'bad' => $totalSurveys > 0 ? round(($bad / $totalSurveys) * 100, 1) : 0,
                        ]
                    ]
                ],
                'surveys_per_day' => $surveysPerDay,
                'top_consultores' => $topConsultores,
                'top_sedes' => $topSedes,
                'recent_surveys' => $recentSurveys,
            ]
        ]);
    }

    /**
     * Comparar consultores/sedes
     */
    public function compare(Request $request)
    {
        $userIds = $request->get('user_ids', []);

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Debe proporcionar al menos un ID de usuario'
            ], 422);
        }

        $users = User::whereIn('id', $userIds)
            ->where('role', '!=', 'admin')
            ->get();

        $comparison = $users->map(function ($user) {
            $stats = $user->getStatistics();
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'location' => $user->location,
                'statistics' => $stats,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $comparison
        ]);
    }

    /**
     * Exportar datos para reportes
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth());
        $endDate = $request->get('end_date', Carbon::now());

        $surveys = Survey::with('user')
            ->dateRange($startDate, $endDate)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($survey) {
                return [
                    'id' => $survey->id,
                    'fecha' => $survey->created_at->format('Y-m-d H:i:s'),
                    'evaluado' => $survey->user->name,
                    'tipo' => $survey->user->role,
                    'ubicacion' => $survey->user->location,
                    'calificacion_experiencia' => $survey->experience_rating,
                    'calificacion_texto' => $survey->rating_text,
                    'calificacion_calidad' => $survey->service_quality_rating,
                    'calificacion_tiempo' => $survey->response_time_rating,
                    'recomendacion' => $survey->recommendation_rating,
                    'cliente_nombre' => $survey->client_name,
                    'cliente_email' => $survey->client_email,
                    'comentarios' => $survey->comments,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $surveys
        ]);
    }
}
