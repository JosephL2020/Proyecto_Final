<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    
    {{-- Token CSRF para peticiones AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Gestión de Tickets - Proyecto Final</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f3f4f6;
            transition: background-color 0.3s, color 0.3s;
        }

        .star-select {
            cursor: pointer;
            user-select: none;
            line-height: 1;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: .03em;
        }

        header.app-header {
            background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        }
        /* Fuente más fuerte en la opción activa GR */ 
        .nav-link.active {
        font-weight: 800 !important; /* más grueso que el 600 por defecto */
        color: #1e3a8a !important;   /* azul oscuro, más contraste */
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

<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('tickets.index') }}">
            Sistema de Tickets
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}"
                           href="{{ route('tickets.index') }}">
                            Tickets
                        </a>
                    </li>

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

            <ul class="navbar-nav">
                @guest
                    @if(!request()->routeIs('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                Iniciar sesión
                            </a>
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
                    <li class="nav-item d-flex align-items-center me-2">
                        <span class="nav-link">
                            {{ auth()->user()->name }} - {{ $rol }}
                        </span>
                    </li>

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm">
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-3">
    @if(session('ok'))
        <div class="alert alert-success border-0 shadow-sm">
            {{ session('ok') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
