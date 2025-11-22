@extends('layouts.app')

@section('content')
<h3 class="mb-3">Nuevo usuario</h3>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="m-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.store') }}" class="card card-body">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input
            name="name"
            value="{{ old('name') }}"
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
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input
            name="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            required
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
            <option value="Manager"  @selected(old('role') === 'Manager')>Gerente IT</option>
            <option value="IT"       @selected(old('role') === 'IT')>Soporte IT</option>
            <option value="Empleado" @selected(old('role') === 'Empleado')>Empleado</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input
            class="form-check-input"
            type="checkbox"
            name="is_active"
            id="is_active"
            value="1"
            @checked(old('is_active', true))
        >
        <label class="form-check-label" for="is_active">
            Usuario activo
        </label>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Guardar</button>
        <a class="btn btn-light" href="{{ route('users.index') }}">Cancelar</a>
    </div>
</form>
@endsection
