<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\User;
use Carbon\Carbon;

class DashboardWebController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas (por defecto último mes)
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Totales generales
        $totalSurveys = Survey::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalConsultores = User::where('role', 'consultor')->where('is_active', true)->count();
        $totalSedes = User::where('role', 'sede')->where('is_active', true)->count();

        // Distribución de calificaciones
        $surveys = Survey::whereBetween('created_at', [$startDate, $endDate])->get();
        $excellent = $surveys->where('experience_rating', 4)->count();
        $good = $surveys->where('experience_rating', 3)->count();
        $regular = $surveys->where('experience_rating', 2)->count();
        $bad = $surveys->where('experience_rating', 1)->count();

        $avgRating = $totalSurveys > 0 ? $surveys->avg('experience_rating') : 0;

        // Top 5 consultores
        $topConsultores = User::where('role', 'consultor')
            ->where('is_active', true)
            ->get()
            ->map(function ($user) {
                $stats = $user->getStatistics();
                return [
                    'user' => $user,
                    'stats' => $stats,
                ];
            })
            ->sortByDesc('stats.average_rating')
            ->take(5);

        // Top 5 sedes
        $topSedes = User::where('role', 'sede')
            ->where('is_active', true)
            ->get()
            ->map(function ($user) {
                $stats = $user->getStatistics();
                return [
                    'user' => $user,
                    'stats' => $stats,
                ];
            })
            ->sortByDesc('stats.average_rating')
            ->take(5);

        // Encuestas recientes
        $recentSurveys = Survey::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $avgColor =
            $avgRating >= 3.50 ? 'green' : ($avgRating >= 2.50 ? '#9c780cff' : '#c13b47ff');


        return view('dashboard.index', compact(
            'totalSurveys',
            'totalConsultores',
            'totalSedes',
            'avgRating',
            'excellent',
            'good',
            'regular',
            'bad',
            'topConsultores',
            'topSedes',
            'recentSurveys',
            'startDate',
            'endDate',
            'avgColor'
        ));
    }
}
