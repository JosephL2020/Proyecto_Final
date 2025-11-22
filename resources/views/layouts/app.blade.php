<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    {{-- Token CSRF para peticiones AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Gestión de Tickets - Proyecto Final</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f3f4f6;
            transition: background-color 0.3s, color 0.3s;
        }

        /* NAVBAR CORPORATIVO */
        .navbar-custom {
            font-size: 0.95rem;
            min-height: 60px;
        }

        .navbar-custom .nav-link {
            padding: 0.4rem 0.85rem;
            font-weight: 500;
            color: #374151;
        }

        .navbar-custom .nav-link:hover {
            color: #1d4ed8;
        }

        .navbar-custom .nav-link.active {
            font-weight: 700;
            color: #1e3a8a !important;
            border-bottom: 2px solid #1d4ed8;
            border-radius: 0;
        }

        .navbar-user-meta {
            line-height: 1.1;
            font-size: 0.8rem;
        }

        .navbar-user-meta .name {
            font-weight: 600;
            color: #111827;
        }

        .navbar-user-meta .role {
            color: #6b7280;
        }

        /* ============================
               MODO OSCURO
        ============================= */

        body.dark-mode {
            background-color: #111827;
            color: #e5e7eb;
        }

        body.dark-mode .navbar,
        body.dark-mode .navbar.bg-white {
            background-color: #020617 !important;
            color: #e5e7eb;
            border-color: #1f2937 !important;
        }

        body.dark-mode .navbar-custom .nav-link {
            color: #e5e7eb;
        }

        body.dark-mode .navbar-custom .nav-link.active {
            color: #93c5fd !important;
            border-bottom-color: #3b82f6;
        }

        body.dark-mode .navbar-user-meta .name {
            color: #f9fafb;
        }

        body.dark-mode .navbar-user-meta .role {
            color: #9ca3af;
        }

        body.dark-mode .dropdown-menu {
            background-color: #020617;
            color: #e5e7eb;
        }

        body.dark-mode .dropdown-item {
            color: #e5e7eb;
        }

        /* TARJETAS / PANEL DE CONTROL */
        body.dark-mode .card,
        body.dark-mode .card-body,
        body-dark-mode .card-header,
        body-dark-mode .table,
        body-dark-mode .modal-content {
            background-color: #111827 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }

        body.dark-mode h1,
        body-dark-mode h2,
        body-dark-mode h3,
        body-dark-mode h4,
        body-dark-mode h5,
        body-dark-mode h6 {
            color: #f9fafb !important;
        }

        body-dark-mode .text-muted {
            color: #d1d5db !important;
        }

        /* TABLAS */
        body-dark-mode .table thead {
            background-color: #020617;
            color: #e5e7eb;
        }

        body-dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
            --bs-table-accent-bg: #1f2937;
            color: #f3f4f6;
        }

        /* DIVISIONES */
        body-dark-mode .border,
        body-dark-mode .border-top,
        body-dark-mode .border-bottom,
        body-dark-mode .border-start,
        body-dark-mode .border-end {
            border-color: #4b5563 !important;
        }

        body-dark-mode hr {
            border-top-color: #4b5563;
        }

        /* BOTONES */
        body-dark-mode .btn-outline-primary {
            border-color: #60a5fa;
            color: #bfdbfe;
        }

        body-dark-mode .btn-outline-danger {
            border-color: #f87171;
            color: #fecaca;
        }

        body-dark-mode .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        /* ALERTAS */
        body-dark-mode .alert {
            background-color: #111827;
            color: #e5e7eb;
            border-color: #374151;
        }

        /* INPUTS */
        body-dark-mode .form-control,
        body-dark-mode .form-select {
            background-color: #020617;
            color: #e5e7eb;
            border-color: #374151;
        }

        body-dark-mode .form-control::placeholder {
            color: #9ca3af;
        }

        /* SOLUCIÓN PARA SVG GIGANTE */
        svg.w-5.h-5 {
            width: 20px !important;
            height: 20px !important;
        }
    </style>
</head>
<body>

{{-- NAVBAR PRINCIPAL (NO SE MUESTRA EN LOGIN) --}}
@if (!request()->routeIs('login'))
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm mb-4 navbar-custom">
    <div class="container-fluid">

        {{-- Marca (por ahora vacía, por si luego agregamos logo) --}}
        <a class="navbar-brand me-3" href="{{ route('tickets.index') }}">
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
                                Dashboard
                            </a>
                        </li>
                    @endif

                    {{-- Tickets (para todos los roles autenticados) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}"
                           href="{{ route('tickets.index') }}">
                            Tickets
                        </a>
                    </li>

                    {{-- Usuarios (solo Manager) --}}
                    @if(auth()->user()->isManager())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                               href="{{ route('users.index') }}">
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
                        <a class="btn btn-sm btn-outline-primary rounded-pill px-3"
                           href="{{ route('login') }}">
                            Iniciar sesión
                        </a>
                    @endif
                @else
                    @php
                        $rol = match(auth()->user()->role) {
                            'Manager' => 'Gerente de IT',
                            'IT'      => 'IT',
                            default   => 'Empleado'
                        };
                    @endphp

                    <div class="navbar-user-meta text-end me-1">
                        <div class="name">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="role">
                            {{ $rol }}
                        </div>
                    </div>

                    <button type="button" id="themeToggle"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        🌙 Modo oscuro
                    </button>

                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            Cerrar sesión
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
        <div class="alert alert-success shadow-sm">
            {{ session('ok') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function () {
        const STORAGE_KEY = 'ticket_system_theme';
        const body = document.body;
        const toggleBtn = document.getElementById('themeToggle');

        if (toggleBtn) {
            const savedTheme = localStorage.getItem(STORAGE_KEY);
            if (savedTheme === 'dark') {
                body.classList.add('dark-mode');
                toggleBtn.innerHTML = '☀️ Modo claro';
            }

            toggleBtn.addEventListener('click', function () {
                const isDark = body.classList.toggle('dark-mode');
                localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
                this.innerHTML = isDark ? '☀️ Modo claro' : '🌙 Modo oscuro';
            });
        }
    })();
</script>

@stack('scripts')
</body>
</html>
