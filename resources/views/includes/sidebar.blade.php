<aside class="sidebar expanded" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-start justify-content-between">
            <div class="sidebar-header-text">
                <div class="sidebar-logo"><img src="{{ asset('assets/img/logo.png')}}" alt=""></div>
                <div class="sidebar-subtitle">Feedback Faces</div>
            </div>
            <button class="collapse-btn d-lg-block d-none" id="collapseBtn">
                <i class="bi-chevron-bar-left bi"></i>
            </button>
        </div>
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="menu-item active">
            <i class="bi bi-bar-chart-fill"></i>
            <span class="menu-text">Resumen general</span>
        </a>
        <a href="{{ route('dashboard.detallezona') }}" class="menu-item">
            <i class="bi bi-geo-alt-fill"></i>
            <span class="menu-text">Detalles por zona</span>
        </a>
        <a href="#" class="menu-item">
            <i class="bi bi-bell-fill"></i>
            <span class="menu-text">Alertas de atención</span>
        </a>
        <a href="#" class="menu-item">
            <i class="bi bi-graph-up-arrow"></i>
            <span class="menu-text">Tendencias y predicciones</span>
        </a>
        <a href="#" class="menu-item">
            <i class="bi bi-award-fill"></i>
            <span class="menu-text">Reconocimientos por zona</span>
        </a>
    </nav>
</aside>
