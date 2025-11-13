@extends('layouts.app')

@section('title', 'Dashboard - TRIMAX')

@section('content')
<h2 style="margin-bottom: 20px; color: #333;">Dashboard</h2>

<!-- Filtros de fecha -->
<div class="card">
    <form method="GET" action="{{ route('dashboard') }}">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0; flex: 1;">
                <label>Fecha Inicio</label>
                <input type="date" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="form-group" style="margin: 0; flex: 1;">
                <label>Fecha Fin</label>
                <input type="date" name="end_date" value="{{ $endDate }}">
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
</div>

<!-- Estadísticas principales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $totalSurveys }}</div>
        <div class="stat-label">Total Encuestas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $totalConsultores }}</div>
        <div class="stat-label">Consultores Activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $totalSedes }}</div>
        <div class="stat-label">Sedes Activas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ number_format($avgRating, 2) }}</div>
        <div class="stat-label">Calificación Promedio</div>
    </div>
</div>

<!-- Distribución de calificaciones -->
<div class="card">
    <div class="card-header">Distribución de Calificaciones</div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
        <div style="text-align: center; padding: 15px; background: #d4edda; border-radius: 4px;">
            <div style="font-size: 36px;">😊</div>
            <div style="font-size: 24px; font-weight: bold; color: #155724;">{{ $excellent }}</div>
            <div style="color: #155724;">Excelente</div>
            @if($totalSurveys > 0)
                <div style="font-size: 12px; color: #155724; margin-top: 5px;">
                    {{ number_format(($excellent / $totalSurveys) * 100, 1) }}%
                </div>
            @endif
        </div>
        <div style="text-align: center; padding: 15px; background: #cfe2ff; border-radius: 4px;">
            <div style="font-size: 36px;">🙂</div>
            <div style="font-size: 24px; font-weight: bold; color: #084298;">{{ $good }}</div>
            <div style="color: #084298;">Bueno</div>
            @if($totalSurveys > 0)
                <div style="font-size: 12px; color: #084298; margin-top: 5px;">
                    {{ number_format(($good / $totalSurveys) * 100, 1) }}%
                </div>
            @endif
        </div>
        <div style="text-align: center; padding: 15px; background: #fff3cd; border-radius: 4px;">
            <div style="font-size: 36px;">😐</div>
            <div style="font-size: 24px; font-weight: bold; color: #664d03;">{{ $regular }}</div>
            <div style="color: #664d03;">Regular</div>
            @if($totalSurveys > 0)
                <div style="font-size: 12px; color: #664d03; margin-top: 5px;">
                    {{ number_format(($regular / $totalSurveys) * 100, 1) }}%
                </div>
            @endif
        </div>
        <div style="text-align: center; padding: 15px; background: #f8d7da; border-radius: 4px;">
            <div style="font-size: 36px;">😞</div>
            <div style="font-size: 24px; font-weight: bold; color: #842029;">{{ $bad }}</div>
            <div style="color: #842029;">Malo</div>
            @if($totalSurveys > 0)
                <div style="font-size: 12px; color: #842029; margin-top: 5px;">
                    {{ number_format(($bad / $totalSurveys) * 100, 1) }}%
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Top Consultores y Sedes -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Top Consultores -->
    <div class="card">
        <div class="card-header">Top 5 Consultores</div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th style="text-align: center;">Encuestas</th>
                    <th style="text-align: center;">Promedio</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topConsultores as $item)
                <tr>
                    <td>
                        <a href="{{ route('users.show', $item['user']->id) }}" style="color: #1a73e8; text-decoration: none;">
                            {{ $item['user']->name }}
                        </a>
                        @if($item['stats']['sedes_count'] > 0)
                            <br><small style="color: #666;">{{ $item['stats']['sedes_count'] }} sedes</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item['stats']['total'] }}</td>
                    <td style="text-align: center;">
                        <strong style="color: #1a73e8;">{{ number_format($item['stats']['average_rating'], 2) }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #666;">No hay datos</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Sedes -->
    <div class="card">
        <div class="card-header">Top 5 Sedes</div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th style="text-align: center;">Encuestas</th>
                    <th style="text-align: center;">Promedio</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topSedes as $item)
                <tr>
                    <td>
                        <a href="{{ route('users.show', $item['user']->id) }}" style="color: #1a73e8; text-decoration: none;">
                            {{ $item['user']->name }}
                        </a>
                        @if($item['user']->consultor)
                            <br><small style="color: #666;">Consultor: {{ $item['user']->consultor->name }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item['stats']['total'] }}</td>
                    <td style="text-align: center;">
                        <strong style="color: #1a73e8;">{{ number_format($item['stats']['average_rating'], 2) }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #666;">No hay datos</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Encuestas Recientes -->
<div class="card">
    <div class="card-header">Encuestas Recientes</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Evaluado</th>
                <th>Cliente</th>
                <th style="text-align: center;">Calificación</th>
                <th>Comentarios</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentSurveys as $survey)
            <tr>
                <td>{{ $survey->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('users.show', $survey->user_id) }}" style="color: #1a73e8; text-decoration: none;">
                        {{ $survey->user->name }}
                    </a>
                    <br>
                    <span class="badge badge-{{ $survey->user->role }}">{{ ucfirst($survey->user->role) }}</span>
                </td>
                <td>{{ $survey->client_name ?: 'Anónimo' }}</td>
                <td style="text-align: center;">
                    <span style="font-size: 24px;">{{ $survey->rating_emoji }}</span>
                    <br>
                    <small>{{ $survey->rating_text }}</small>
                </td>
                <td>{{ Str::limit($survey->comments, 50) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #666;">No hay encuestas recientes</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection