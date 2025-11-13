<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TRIMAX - Panel Admin')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        
        /* Header */
        .header { background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #1a73e8; font-size: 24px; }
        .header-right { display: flex; gap: 20px; align-items: center; }
        .user-info { color: #666; }
        .btn-logout { padding: 8px 16px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-logout:hover { background: #c82333; }
        
        /* Navigation */
        .nav { background: white; padding: 0 30px; border-bottom: 1px solid #e0e0e0; }
        .nav ul { list-style: none; display: flex; gap: 30px; }
        .nav a { display: block; padding: 15px 0; color: #666; text-decoration: none; border-bottom: 3px solid transparent; }
        .nav a:hover, .nav a.active { color: #1a73e8; border-bottom-color: #1a73e8; }
        
        /* Container */
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        /* Alerts */
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Buttons */
        .btn { padding: 10px 20px; border-radius: 4px; text-decoration: none; display: inline-block; border: none; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #1a73e8; color: white; }
        .btn-primary:hover { background: #1557b0; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        
        /* Cards */
        .card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #333; }
        
        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-value { font-size: 32px; font-weight: bold; color: #1a73e8; }
        .stat-label { color: #666; font-size: 14px; margin-top: 5px; }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f8f9fa; font-weight: 600; color: #333; }
        tr:hover { background: #f8f9fa; }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #333; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #1a73e8; }
        .form-actions { display: flex; gap: 10px; margin-top: 20px; }
        
        /* Badge */
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .badge-consultor { background: #e3f2fd; color: #1976d2; }
        .badge-sede { background: #f3e5f5; color: #7b1fa2; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        
        /* Pagination */
        .pagination { display: flex; gap: 5px; margin-top: 20px; justify-content: center; }
        .pagination a, .pagination span { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333; }
        .pagination .active { background: #1a73e8; color: white; border-color: #1a73e8; }
        .pagination a:hover { background: #f0f2f5; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="header">
        <h1>🔷 TRIMAX</h1>
        <div class="header-right">
            <span class="user-info">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <nav class="nav">
        <ul>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Usuarios</a></li>
        </ul>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>