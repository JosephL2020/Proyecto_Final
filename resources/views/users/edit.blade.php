@extends('layouts.app')

@section('content')
<h3 class="mb-3">Editar usuario</h3>

@if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="m-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.update', $user) }}" class="card card-body">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input 
            name="name"
            value="{{ old('name', $user->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input
            name="email"
            type="email"
            value="{{ old('email', $user->email) }}"
            class="form-control @error('email') is-invalid @enderror"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input
            name="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="Dejar en blanco para no cambiar"
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Rol</label>
        <select
            name="role"
            class="form-select @error('role') is-invalid @enderror"
            required
        >
            <option value="">Seleccione un rol...</option>
            <option value="Manager"  @selected(old('role', $user->role) === 'Manager')>Gerente IT</option>
            <option value="IT"       @selected(old('role', $user->role) === 'IT')>Soporte IT</option>
            <option value="Empleado" @selected(old('role', $user->role) === 'Empleado')>Empleado</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            class="form-check-input"
            value="1"
            @checked(old('is_active', $user->is_active))
        >
        <label class="form-check-label" for="is_active">
            Usuario activo
        </label>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Actualizar</button>
        <a class="btn btn-light" href="{{ route('users.index') }}">Cancelar</a>
    </div>
</form>
@endsection
