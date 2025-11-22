@extends('layouts.app')

@section('content')

@php
    use App\Models\User;
    use App\Models\Ticket;

    // Estadísticas globales
    $totalUsers     = User::count();
    $activeUsers    = User::where('is_active', true)->count();
    $managerCount   = User::where('role', 'Manager')->count();
    $itCount        = User::where('role', 'IT')->count();
    $employeeCount  = User::where('role', 'Empleado')->count();
@endphp

<style>
    /* ====== ESTILOS BASE ====== */

    .role-badge {
        border-radius: 999px;
        font-size: .75rem;
        padding: .2rem .6rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 86px;
    }
    .role-manager {
        background-color: #e0f2fe;
        color: #0369a1;
    }
    .role-it {
        background-color: #f5e0ff;
        color: #6b21a8;
    }
    .role-employee {
        background-color: #e5e7eb;
        color: #374151;
    }

    .users-card {
        border: 0;
        border-radius: .75rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25);
        overflow: hidden;
        background: #ffffff;
    }

    .users-card-body {
        padding: 0;
    }

    .users-card-footer {
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }

    .users-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .users-thead th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding-top: .9rem;
        padding-bottom: .9rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #6b7280;
        white-space: nowrap;
    }

    .users-row td {
        padding-top: .9rem;
        padding-bottom: .9rem;
        border-top: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .users-row:first-child td {
        border-top: none;
    }

    .users-row:hover td {
        background: #f3f4f6;
    }

    .inactive-row {
        opacity: .8;
    }

    /* ====== MODO OSCURO ====== */

    body.dark-mode .users-card {
        background: #020617;
        border: 1px solid #111827;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.65);
    }

    body.dark-mode .users-card-footer {
        background: #020617;
        border-top-color: #1f2937;
        color: #9ca3af;
    }

    body.dark-mode .users-table,
    body.dark-mode .users-table thead,
    body.dark-mode .users-table tbody,
    body.dark-mode .users-table tr,
    body.dark-mode .users-table th,
    body.dark-mode .users-table td {
        background-color: #020617 !important;
        border-color: #1f2937 !important;
        color: #e5e7eb !important;
    }

    body.dark-mode .users-thead th {
        background: #020617 !important;
        color: #9ca3af !important;
    }

    body.dark-mode .users-row td {
        background-color: #020617 !important;
    }

    body.dark-mode .users-row:hover td {
        background-color: #0f172a !important;
    }

    body.dark-mode .inactive-row td {
        background-color: #111827 !important;
        color: #d1d5db !important;
    }

    /* Que los textos “secundarios” no se pierdan */
    body.dark-mode .users-table .text-muted {
        color: #9ca3af !important;
    }

    /* Botones mantienen buen contraste */
    body.dark-mode .btn-outline-primary {
        border-color: #3b82f6;
        color: #bfdbfe;
    }
    body.dark-mode .btn-outline-primary:hover {
        background-color: #1d4ed8;
        color: #e5e7eb;
    }
</style>

{{-- Encabezado general --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">Gestión de usuarios</h3>
        <div class="text-muted small">
            Administra roles, estado y actividad de los usuarios del sistema de tickets.
        </div>
    </div>
</div>

{{-- Título + Botón de nuevo usuario --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Usuarios</h1>

    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo usuario
    </a>
</div>

@if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Estadísticas --}}
<div class="row g-2 mb-3">
    <div class="col-auto">
        <span class="badge rounded-pill text-bg-primary px-3 py-2">
            Total: {{ $totalUsers }}
        </span>
    </div>
    <div class="col-auto">
        <span class="badge rounded-pill text-bg-success px-3 py-2">
            Activos: {{ $activeUsers }}
        </span>
    </div>
    <div class="col-auto">
        <span class="badge rounded-pill text-bg-secondary px-3 py-2">
            Gerentes IT: {{ $managerCount }}
        </span>
    </div>
    <div class="col-auto">
        <span class="badge rounded-pill text-bg-info px-3 py-2">
            Soporte IT: {{ $itCount }}
        </span>
    </div>
    <div class="col-auto">
        <span class="badge rounded-pill text-bg-light border px-3 py-2">
            Empleados: {{ $employeeCount }}
        </span>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" class="card mb-3">
    <div class="card-body row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small">Buscar</label>
            <input type="text"
                   name="s"
                   value="{{ request('s') }}"
                   class="form-control"
                   placeholder="Nombre o correo...">
        </div>

        <div class="col-md-3">
            <label class="form-label small">Rol</label>
            <select name="role" class="form-select">
                <option value="">Todos</option>
                <option value="Manager"  @selected(request('role') === 'Manager')>Gerente IT</option>
                <option value="IT"       @selected(request('role') === 'IT')>Soporte IT</option>
                <option value="Empleado" @selected(request('role') === 'Empleado')>Empleado</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Estado</label>
            <select name="active" class="form-select">
                <option value="">Todos</option>
                <option value="1" @selected(request('active') === '1')>Activos</option>
                <option value="0" @selected(request('active') === '0')>Inactivos</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100">Filtrar</button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </div>
</form>

<div class="card users-card">
    <div class="card-body users-card-body">
        <table class="table users-table align-middle mb-0">
            <thead class="users-thead">
<style>
    .role-badge {
        border-radius: 999px;
        font-size: .75rem;
        padding: .2rem .6rem;
        font-weight: 500;
    }
    .role-manager {
        background-color: #e0f2fe;
        color: #0369a1;
    }
    .role-it {
        background-color: #f5e0ff;
        color: #6b21a8;
    }
    .role-employee {
        background-color: #e5e7eb;
        color: #374151;
    }
</style>

{{-- Tabla --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Actividad (tickets)</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    @php
                        $role = $u->role;
                        $roleClass = 'role-employee';
                        $roleLabel = 'Empleado';

                        if ($role === 'Manager') {
                            $roleClass = 'role-manager';
                            $roleLabel = 'Gerente IT';
                        } elseif ($role === 'IT') {
                            $roleClass = 'role-it';
                            $roleLabel = 'Soporte IT';
                        }

                        $ticketsCreated = Ticket::where('created_by', $u->id)->count();
                        $ticketsAssigned = Ticket::where('assigned_to', $u->id)->count();
                        $ticketsClosed = Ticket::where('assigned_to', $u->id)
                            ->whereIn('status', ['resolved','closed'])
                            ->count();
                    @endphp

                    <tr class="users-row {{ $u->is_active ? '' : 'inactive-row' }}">
                        <td>
                            <div class="fw-semibold">{{ $u->name }}</div>
                            <div class="small text-muted">
                                ID #{{ $u->id }}
                            </div>
                        </td>
                        <td>
                            <span class="small">{{ $u->email }}</span>
                        </td>
                        <td>
                            <span class="role-badge {{ $roleClass }}">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td>
                            @if($u->is_active)
                                <span class="badge rounded-pill text-bg-success">Activo</span>
                            @else
                                <span class="badge rounded-pill text-bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="small text-muted">
                                Creados: <span class="fw-semibold">{{ $ticketsCreated }}</span> ·
                                Asignados: <span class="fw-semibold">{{ $ticketsAssigned }}</span> ·
                                Cerrados: <span class="fw-semibold">{{ $ticketsClosed }}</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <form method="POST"
                                      action="{{ route('users.toggle-active', $u) }}"
                                      onsubmit="return confirm('¿Seguro que deseas cambiar el estado de este usuario?');">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $u->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay usuarios para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer users-card-footer">
        {{ $users->links() }}
    </div>
</div>

@endsection
