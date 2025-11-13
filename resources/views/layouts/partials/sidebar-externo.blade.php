<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="/home-externo" class="brand-link text-center">
        <span class="brand-text font-weight-light">UNI2</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">

                <li class="nav-item">
                    <a href="/home-externo" class="nav-link {{ request()->is('home-externo') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Inicio</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/externo/eventos" class="nav-link {{ request()->is('externo/eventos*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>Eventos</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/externo/reportes" class="nav-link {{ request()->is('externo/reportes') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Reportes</p>
                    </a>
                </li>

                <li class="nav-header">OTRAS OPCIONES</li>

                <li class="nav-item">
                    <a href="/home-publica" class="nav-link">
                        <i class="nav-icon fas fa-globe"></i>
                        <p>Ir a página pública</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/login" onclick="localStorage.clear()" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Cerrar sesión</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>

</aside>
