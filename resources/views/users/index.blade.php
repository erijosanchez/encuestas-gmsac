@extends('layouts.app')

@section('title', 'Usuarios - TRIMAX')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333;">Gestión de Usuarios</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">+ Crear Usuario</a>
    </div>

    <!-- Filtros -->
    <div class="card">
        <form method="GET" action="{{ route('users.index') }}">
            <div style="display: flex; gap: 15px; align-items: end;">
                <div class="form-group" style="margin: 0; flex: 1;">
                    <label>Buscar</label>
                    <input type="text" name="search" placeholder="Nombre o email..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin: 0; width: 200px;">
                    <label>Tipo</label>
                    <select name="role">
                        <option value="">Todos</option>
                        <option value="consultor" {{ request('role') == 'consultor' ? 'selected' : '' }}>Consultores
                        </option>
                        <option value="sede" {{ request('role') == 'sede' ? 'selected' : '' }}>Sedes</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                @if (request('search') || request('role'))
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th style="text-align: center;">Encuestas</th>
                    <th style="text-align: center;">Promedio</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if ($user->role == 'sede' && $user->consultor)
                                <br><small style="color: #666;">Consultor: {{ $user->consultor->name }}</small>
                            @endif
                            @if ($user->role == 'consultor' && $user->statistics['sedes_count'] > 0)
                                <br><small style="color: #666;">{{ $user->statistics['sedes_count'] }} sedes
                                    asignadas</small>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td style="text-align: center;">{{ $user->statistics['total'] }}</td>
                        <td style="text-align: center;">
                            <strong
                                style="color: #1a73e8;">{{ number_format($user->statistics['average_rating'], 2) }}</strong>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-primary"
                                style="padding: 5px 10px; font-size: 12px;">Ver</a>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary"
                                style="padding: 5px 10px; font-size: 12px;">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                            No hay usuarios registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="pagination">
                @if ($users->onFirstPage())
                    <span style="color: #ccc;">« Anterior</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}">« Anterior</a>
                @endif

                @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if ($page == $users->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}">Siguiente »</a>
                @else
                    <span style="color: #ccc;">Siguiente »</span>
                @endif
            </div>
        @endif
    </div>
@endsection
