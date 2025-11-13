@extends('layouts.app')

@section('title', 'Crear Usuario - TRIMAX')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333;">Crear Usuario</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">« Volver</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nombre *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                <small style="color: #666;">Mínimo 8 caracteres</small>
                @error('password')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">Tipo *</label>
                <select id="role" name="role" required>
                    <option value="">Seleccionar...</option>
                    <option value="consultor" {{ old('role') == 'consultor' ? 'selected' : '' }}>Consultor</option>
                    <option value="sede" {{ old('role') == 'sede' ? 'selected' : '' }}>Sede</option>
                </select>
                @error('role')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group" id="consultor-field" style="display: none;">
                <label for="consultor_id">Consultor Asignado</label>
                <select id="consultor_id" name="consultor_id">
                    <option value="">Sin asignar</option>
                    @foreach ($consultores as $consultor)
                        <option value="{{ $consultor->id }}"
                            {{ old('consultor_id') == $consultor->id ? 'selected' : '' }}>
                            {{ $consultor->name }}
                        </option>
                    @endforeach
                </select>
                <small style="color: #666;">Solo para sedes</small>
                @error('consultor_id')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="location">Ubicación</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}"
                    placeholder="Dirección física">
                <small style="color: #666;">Solo para sedes</small>
                @error('location')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Mostrar/ocultar campo consultor según tipo
            const roleSelect = document.getElementById('role');
            const consultorField = document.getElementById('consultor-field');

            roleSelect.addEventListener('change', function() {
                if (this.value === 'sede') {
                    consultorField.style.display = 'block';
                } else {
                    consultorField.style.display = 'none';
                }
            });

            // Verificar al cargar
            if (roleSelect.value === 'sede') {
                consultorField.style.display = 'block';
            }
        </script>
    @endpush
@endsection
