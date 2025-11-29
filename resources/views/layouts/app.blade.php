<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    {{-- Token CSRF para peticiones AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TicketFlow - Sistema de Gestión de Tickets</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f3f4f6;
            transition: background-color 0.3s, color 0.3s;
        }

        /* NAVBAR CORPORATIVO MEJORADO */
        .navbar-custom {
            font-size: 0.95rem;
            min-height: 60px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
        }

        .navbar-custom .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-custom .navbar-brand:hover {
            color: #f0f9ff !important;
        }

        .navbar-custom .nav-link {
            padding: 0.4rem 0.85rem;
            font-weight: 500;
            color: #e0f2fe !important;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .navbar-custom .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-custom .nav-link.active {
            font-weight: 600;
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 0.375rem;
        }

        .navbar-user-meta {
            line-height: 1.2;
            font-size: 0.8rem;
        }

        .navbar-user-meta .name {
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

     .navbar-user-meta .email {
    color: #e0f2fe !important;
    font-size: 0.75rem;
    font-weight: 400;
}


        /* Botones en navbar */
        .navbar-custom .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .navbar-custom .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        /* ============================
               MODO OSCURO
        ============================= */

        body.dark-mode {
            background-color: #111827;
            color: #e5e7eb;
        }

        /* Navbar en modo oscuro */
        body.dark-mode .navbar-custom {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            border-bottom-color: #334155 !important;
        }

        body.dark-mode .navbar-custom .nav-link {
            color: #cbd5e1 !important;
        }

        body.dark-mode .navbar-custom .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .navbar-custom .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .navbar-user-meta .name {
            color: #f1f5f9;
        }

        body.dark-mode .navbar-user-meta .role {
            color: #94a3b8;
        }

        /* Resto de tus estilos dark mode se mantienen igual... */
        body.dark-mode .dropdown-menu {
            background-color: #1e293b;
            color: #e5e7eb;
        }

        body.dark-mode .dropdown-item {
            color: #e5e7eb;
        }

        body.dark-mode .dropdown-item:hover {
            background-color: #334155;
        }

        /* TABLAS - dark mode */
        body.dark-mode .table {
            --bs-table-bg: transparent;
            --bs-table-color: #f9fafb; 
            --bs-table-border-color: #4b5563;
        }

        body.dark-mode .table thead {
            background-color: #1f2937 !important; 
        }

        body.dark-mode .table th {
            background-color: #1f2937 !important;
            color: #f9fafb !important; 
            border-bottom: 2px solid #374151 !important;
            font-weight: 600;
        }

        body.dark-mode .table td {
            color: #e5e7eb !important; 
            border-color: #374151 !important;
            background-color: #111827 !important;
        }

        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
            --bs-table-accent-bg: #1a2231 !important; 
            color: #e5e7eb !important;
        }

        body.dark-mode .table-striped > tbody > tr:nth-of-type(even) > * {
            background-color: #111827 !important; 
            color: #e5e7eb !important;
        }

        body.dark-mode .table-hover tbody tr:hover td {
            background-color: #374151 !important; 
            color: #ffffff !important;
        }

        /* Textos específicos en tablas */
        body.dark-mode .table .text-muted {
            color: #9ca3af !important;
        }

        body.dark-mode .table a {
            color: #60a5fa !important; 
        }

        body.dark-mode .table a:hover {
            color: #93c5fd !important;
        }

        /* TARJETAS / PANEL DE CONTROL */
        body.dark-mode .card,
        body.dark-mode .card-body,
        body.dark-mode .card-header,
        body.dark-mode .table,
        body.dark-mode .modal-content {
            background-color: #111827 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }

        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6 {
            color: #f9fafb !important;
        }

        body.dark-mode .text-muted {
            color: #d1d5db !important;
        }

        /* BOTONES */
        body.dark-mode .btn-outline-primary {
            border-color: #60a5fa;
            color: #bfdbfe;
        }

        body.dark-mode .btn-outline-danger {
            border-color: #f87171;
            color: #fecaca;
        }

        body.dark-mode .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        /* ALERTAS */
        body.dark-mode .alert {
            background-color: #111827;
            color: #e5e7eb;
            border-color: #374151;
        }

        /* INPUTS */
        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: #020617;
            color: #e5e7eb;
            border-color: #374151;
        }

        body.dark-mode .form-control::placeholder {
            color: #9ca3af;
        }

        /* PAGINACIÓN */
        .flex.justify-between.flex-1.sm\:hidden {
            display: none;
        }

        .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {
            display: flex;
        }

        @media (max-width: 640px) {
            .flex.justify-between.flex-1.sm\:hidden {
                display: flex !important;
            }
            
            .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {
                display: none !important;
            }
        }

    </style>
</head>
<body>

{{-- NAVBAR PRINCIPAL (NO SE MUESTRA EN LOGIN) --}}
@if (!request()->routeIs('login'))
<nav class="navbar navbar-expand-lg navbar-custom border-bottom shadow-sm mb-4">
    <div class="container-fluid">

        {{-- MARCA CON TICKETFLOW --}}
        <a class="navbar-brand me-4" href="{{ route('tickets.index') }}">
            <i class="bi bi-ticket-perforated"></i>
            TicketFlow
        </a>

        {{-- Botón modo móvil --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Contenido del menú --}}
        <div class="collapse navbar-collapse" id="navbarNav">

            {{-- Menú izquierdo --}}
            <ul class="navbar-nav me-auto">

                @auth
                    {{-- Dashboard solo para Manager --}}
                    @if(auth()->user()->isManager())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                               href="{{ route('dashboard.index') }}">
                                <i class="bi bi-speedometer2 me-1"></i>
                                Dashboard
                            </a>
                        </li>
                    @endif

                    {{-- Tickets (para todos los roles autenticados) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}"
                           href="{{ route('tickets.index') }}">
                            <i class="bi bi-ticket-detailed me-1"></i>
                            Tickets
                        </a>
                    </li>

                    {{-- Usuarios (solo Manager) --}}
                    @if(auth()->user()->isManager())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                               href="{{ route('users.index') }}">
                                <i class="bi bi-people me-1"></i>
                                Usuarios
                            </a>
                        </li>
                    @endif
                @endauth

            </ul>

            {{-- Bloque derecho: usuario + tema + logout --}}
            <div class="d-flex align-items-center gap-3">

                @guest
                    @if(!request()->routeIs('login'))
                        <a class="btn btn-sm btn-outline-light rounded-pill px-3"
                           href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Iniciar sesión
                        </a>
                    @endif
                @else
                    @php
                        $rol = match(auth()->user()->role) {
                            'Manager' => 'Gerente IT',
                            'IT'      => 'Soporte IT',
                            'Empleado' => 'Empleado',
                            'User'    => 'Usuario',
                            default   => 'Usuario'
                        };
                        
                        $roleBadge = match(auth()->user()->role) {
                            'Manager' => 'bg-warning text-dark',
                            'IT'      => 'bg-info text-white',
                            'Empleado' => 'bg-secondary text-white',
                            'User'    => 'bg-light text-dark',
                            default   => 'bg-light text-dark'
                        };
                        
                    @endphp

                                <div class="navbar-user-meta text-end me-2">
                    <div class="name d-flex align-items-center gap-2">
                        {{ auth()->user()->name }}
                        <span class="badge {{ $roleBadge }} role-badge">{{ $rol }}</span>
                    </div>
                    <div class="email">
                        {{ auth()->user()->email }}
                    </div>
                </div>
            @php
                $initial = strtoupper(mb_substr(auth()->user()->name, 0, 1));
                $avatarClass = (auth()->user()->role === 'IT' || auth()->user()->role === 'Manager') ? 'avatar-it' : 'avatar-default';
            @endphp

            <div class="d-flex align-items-center gap-2">
                {{-- Avatar circular con inicial --}}
                <div class="avatar-wrapper {{ $avatarClass }}">
                    <div class="avatar-inner">
                        {{ $initial }}
                    </div>
                </div>

                {{-- Nombre y rol --}}
                <div class="navbar-user-meta text-start">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">{{ $rol }}</div>
                </div>
            </div>

                    <button type="button" id="themeToggle"
                            class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bi bi-moon-stars me-1"></i>
                        <span class="theme-text">Modo oscuro</span>
                    </button>

                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <button class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Salir
                        </button>
                    </form>
                @endguest

            </div>

        </div>
    </div>
</nav>
@endif

<div class="container-fluid px-3">
    @if(session('ok'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('ok') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function () {
        const STORAGE_KEY = 'ticketflow_theme';
        const body = document.body;
        const toggleBtn = document.getElementById('themeToggle');
        const themeText = toggleBtn ? toggleBtn.querySelector('.theme-text') : null;

        // Aplicar tema guardado
        const savedTheme = localStorage.getItem(STORAGE_KEY);
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
            if (themeText) themeText.textContent = 'Modo claro';
        }

        // Toggle tema
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const isDark = body.classList.toggle('dark-mode');
                localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
                if (themeText) {
                    themeText.textContent = isDark ? 'Modo claro' : 'Modo oscuro';
                }
            });
        }
    })();
</script>

@stack('scripts')
</body>
</html>