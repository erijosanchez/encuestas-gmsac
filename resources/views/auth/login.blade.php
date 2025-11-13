<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TRIMAX</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { text-align: center; color: #1a73e8; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        input[type="email"], input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        input:focus { outline: none; border-color: #1a73e8; }
        .checkbox-group { display: flex; align-items: center; margin-bottom: 20px; }
        .checkbox-group input { width: auto; margin-right: 8px; }
        button { width: 100%; padding: 12px; background: #1a73e8; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: 500; cursor: pointer; }
        button:hover { background: #1557b0; }
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .alert-danger { background: #fee; color: #c00; border: 1px solid #fcc; }
        .credentials { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px; font-size: 13px; }
        .credentials strong { color: #1a73e8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔷 TRIMAX</h1>
        <p class="subtitle">Sistema de Encuestas - Panel Admin</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', 'admin@trimax.com') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="Trimax2024!" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="margin: 0;">Recordarme</label>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <div class="credentials">
            <strong>Credenciales de prueba:</strong><br>
            Email: admin@trimax.com<br>
            Password: Trimax2024!
        </div>
    </div>
</body>
</html>
