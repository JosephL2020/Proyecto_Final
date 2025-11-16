@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Usuarios</h3>
  <a class="btn btn-primary" href="{{ route('users.create') }}">Nuevo usuario</a>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input name="s" value="{{ $q }}" class="form-control" placeholder="Buscar por nombre o email">
  </div>
  <div class="col-auto">
    <button class="btn btn-outline-secondary">Buscar</button>
  </div>
</form>

@if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              @php
                $map = ['employee'=>'Empleado','it'=>'Soporte Técnico','manager'=>'Gerente de IT'];
              @endphp
              {{ $map[$u->role] ?? $u->role }}
            </td>
            <td class="text-end">
              <a href="{{ route('users.edit',$u) }}" class="btn btn-sm btn-outline-primary">Editar</a>
              <form method="POST" action="{{ route('users.destroy',$u) }}" class="d-inline" onsubmit="return confirm('¿Eliminar usuario?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4"><em>No hay usuarios</em></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $users->links() }}
</div>
@endsection
