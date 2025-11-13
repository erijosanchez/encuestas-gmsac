@extends('layouts.app')

@section('title', 'Detalle Usuario - TRIMAX')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333;">Detalle de Usuario</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary">Editar</a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">« Volver</a>
        </div>
    </div>

    <!-- Información del Usuario -->
    <div class="card">
        <div class="card-header">Información General</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p style="margin-bottom: 10px;"><strong>Nombre:</strong> {{ $user->name }}</p>
                <p style="margin-bottom: 10px;"><strong>Email:</strong> {{ $user->email }}</p>
                <p style="margin-bottom: 10px;"><strong>Teléfono:</strong> {{ $user->phone ?: 'N/A' }}</p>
            </div>
            <div>
                <p style="margin-bottom: 10px;">
                    <strong>Tipo:</strong>
                    <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                </p>
                <p style="margin-bottom: 10px;">
                    <strong>Estado:</strong>
                    <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </p>
                @if ($user->location)
                    <p style="margin-bottom: 10px;"><strong>Ubicación:</strong> {{ $user->location }}</p>
                @endif
            </div>
        </div>

        @if ($user->role == 'sede' && $user->consultor)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                <p><strong>Consultor Asignado:</strong>
                    <a href="{{ route('users.show', $user->consultor->id) }}" style="color: #1a73e8;">
                        {{ $user->consultor->name }}
                    </a>
                </p>
            </div>
        @endif

        @if ($user->role == 'consultor' && $user->sedes->count() > 0)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                <p><strong>Sedes Asignadas:</strong></p>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach ($user->sedes as $sede)
                        <li style="margin-bottom: 5px;">
                            <a href="{{ route('users.show', $sede->id) }}" style="color: #1a73e8;">
                                {{ $sede->name }}
                            </a>
                            @if ($sede->location)
                                <small style="color: #666;">- {{ $sede->location }}</small>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- URL de Encuesta -->
    <div class="card">
        <div class="card-header">URL de Encuesta</div>
        <p style="margin-bottom: 15px; color: #666;">Esta es la URL única para que los clientes evalúen este
            {{ $user->role }}:</p>
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" value="{{ url('/encuesta/' . $user->unique_token) }}" id="survey-url" readonly
                style="flex: 1; padding: 10px; background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 4px;">
            <button onclick="copySurveyUrl()" class="btn btn-primary">Copiar</button>
            <a href="{{ url('/encuesta/' . $user->unique_token) }}" target="_blank" class="btn btn-secondary">Probar</a>
        </div>
        <form method="POST" action="{{ route('users.regenerate-token', $user->id) }}" style="margin-top: 10px;">
            @csrf
            <button type="submit" class="btn btn-danger" style="font-size: 12px; padding: 5px 10px;"
                onclick="return confirm('¿Estás seguro? La URL anterior dejará de funcionar.')">
                Regenerar Token
            </button>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $user->statistics['total'] }}</div>
            <div class="stat-label">Total Encuestas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($user->statistics['average_rating'], 2) }}</div>
            <div class="stat-label">Calificación Promedio</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $user->statistics['excellent'] }}</div>
            <div class="stat-label">Excelentes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $user->statistics['good'] }}</div>
            <div class="stat-label">Buenos</div>
        </div>
    </div>

    <!-- Encuestas Recientes -->
    @if ($user->recent_surveys->count() > 0)
        <div class="card">
            <div class="card-header">Encuestas Recientes</div>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th style="text-align: center;">Calificación</th>
                        <th>Comentarios</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->recent_surveys as $survey)
                        <tr>
                            <td>{{ $survey->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $survey->client_name ?: 'Anónimo' }}</td>
                            <td style="text-align: center;">
                                <span style="font-size: 24px;">{{ $survey->rating_emoji }}</span>
                                <br>
                                <small>{{ $survey->rating_text }}</small>
                            </td>
                            <td>{{ Str::limit($survey->comments, 80) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @push('scripts')
        <script>
            function copySurveyUrl() {
                const input = document.getElementById('survey-url');
                input.select();
                document.execCommand('copy');
                alert('URL copiada al portapapeles');
            }
        </script>
    @endpush
@endsection
