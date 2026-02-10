@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <h3 class="mb-3">Editar departamento</h3>

    <form method="POST" action="{{ route('departments.update', $department) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre del departamento</label>
            <input name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $department->name) }}"
                   required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <hr>

        <h6 class="mb-2">Gerente del departamento</h6>

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="manager_name"
                   class="form-control @error('manager_name') is-invalid @enderror"
                   value="{{ old('manager_name', $department->manager?->name) }}"
                   required>
            @error('manager_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input name="manager_email"
                   type="email"
                   class="form-control @error('manager_email') is-invalid @enderror"
                   value="{{ old('manager_email', $department->manager?->email) }}"
                   required>
            @error('manager_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('departments.index') }}" class="btn btn-light">Cancelar</a>
    </form>
</div>
@endsection
