<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Sistema de Gestión de Tickets - Proyecto Final</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-primary text-white p-2 mb-4">
  <div class="container">
    <h6 class="m-0">Proyecto Final - Sistema de Gestión de Soporte Técnico Grupo 3</h6>
  </div>
</header>

<nav class="navbar navbar-expand-lg bg-light border-bottom mb-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="{{ route('tickets.index') }}">Sistema de Tickets</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('tickets.index') }}">Tickets</a></li>
        @auth
          @if(auth()->user()->isManager())
            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.index') }}">Panel de Control</a></li>
          @endif
        @endauth
      </ul>
      <ul class="navbar-nav">
        @guest
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Ingresar</a></li>
        @else
          @php
            $rol = auth()->user()->role === 'manager' ? 'Gerente de IT' :
                   (auth()->user()->role === 'it' ? 'Soporte Técnico' : 'Empleado');
          @endphp
          <li class="nav-item"><span class="nav-link">{{ auth()->user()->name }} - {{ $rol }}</span></li>
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">@csrf
              <button class="btn btn-outline-danger btn-sm">Salir</button>
            </form>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
