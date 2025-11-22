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

        .navbar-brand {
            font-weight: 700;
            letter-spacing: .03em;
        }

        header.app-header {
            background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        }

        /* ============================
               MODO OSCURO
        ============================= */

        body.dark-mode {
            background-color: #111827;
            color: #e5e7eb;
        }

        body.dark-mode header.app-header {
            background: linear-gradient(90deg, #020617, #111827);
        }

        body.dark-mode .navbar,
        body.dark-mode .navbar.bg-white {
            background-color: #020617 !important;
            color: #e5e7eb;
            border-color: #1f2937 !important;
        }

        body.dark-mode .navbar .navbar-brand {
            color: #e5e7eb;
        }

        body.dark-mode .nav-link {
            color: #e5e7eb;
        }

        body.dark-mode .nav-link.active {
            color: #0d6efd !important;
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

        /* TABLAS */
        body.dark-mode .table thead {
            background-color: #020617;
            color: #e5e7eb;
        }

        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
            --bs-table-accent-bg: #1f2937;
            color: #f3f4f6;
        }

        /* DIVISIONES */
        body.dark-mode .border,
        body.dark-mode .border-top,
        body.dark-mode .border-bottom,
        body.dark-mode .border-start,
        body.dark-mode .border-end {
            border-color: #4b5563 !important;
        }

        body.dark-mode hr {
            border-top-color: #4b5563;
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

                /* SOLUCIÓN PARA SVG GIGANTE - Forzar tamaño de flechas */
        svg.w-5.h-5 {
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            max-width: 20px !important;
            max-height: 20px !important;
        }

        /* Específico para botones de paginación */
        a.relative.inline-flex.items-center.px-2.py-2.text-sm svg {
            width: 20px !important;
            height: 20px !important;
            flex-shrink: 0;
        }

        /* BADGES */
        body.dark-mode .badge.bg-light,
        body.dark-mode .badge.bg-white {
            background-color: #374151 !important;
            color: #e5e7eb !important;
        /* Opción activa en el menú */
        .nav-link.active {
            font-weight: 800 !important;
            color: #1e3a8a !important;
        }
    </style>
</head>
<body>

<header class="app-header text-white py-2 mb-3">
    <div class="container-fluid px-3">
        <h6 class="m-0">
            Proyecto Final - Sistema de Gestión de Soporte Técnico Grupo 3
        </h6>
    </div>
</header>

{{-- NAVBAR PRINCIPAL --}}
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('tickets.index') }}">
            Sistema de Tickets
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

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                       href="{{ route('dashboard.index') }}">
                        Dashboard
                    </a>
                </li>

                {{-- Tickets --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}"
                       href="{{ route('tickets.index') }}">
                        Tickets
                    </a>
                </li>

                {{-- Usuarios (solo Manager) --}}
                @auth
                    @if(auth()->user()->isManager())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                               href="{{ route('users.index') }}">
                                Usuarios
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                               href="{{ route('dashboard.index') }}">
                                Panel de Control
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            {{-- Menú derecho: usuario + sesión --}}
            <ul class="navbar-nav">

                @guest
                    @if(!request()->routeIs('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                        </li>
                    @endif

                @else
                    @php
                        $rol = match(auth()->user()->role) {
                            'Manager' => 'Gerente de IT',
                            'IT'      => 'IT',
                            default   => 'Empleado'
                        };
                    @endphp

                    <li class="nav-item d-flex align-items-center me-3">
                    <li class="nav-item d-flex align-items-center me-2">
                        <span class="nav-link">
                            {{ auth()->user()->name }} - {{ $rol }}
                        </span>
                    </li>

                    <li class="nav-item d-flex align-items-center me-3">
                        <button type="button" id="themeToggle"
                                class="btn btn-outline-secondary btn-sm">
                            🌙 Modo oscuro
                        </button>
                    </li>

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm">Cerrar sesión</button>
                        </form>
                    </li>

                @endguest
            </ul>

        </div>
    </div>
</nav>

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
    })();
</script>

@stack('scripts')
</body>
</html>
