@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Crear departamento</h3>

    <form method="POST" action="{{ route('departments.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre del departamento</label>
            <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <hr>

        <h6 class="mb-2">Gerente del departamento</h6>

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="manager_name" class="form-control @error('manager_name') is-invalid @enderror" value="{{ old('manager_name') }}" required>
            @error('manager_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input name="manager_email" type="email" class="form-control @error('manager_email') is-invalid @enderror" value="{{ old('manager_email') }}" required>
            @error('manager_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">Guardar</button>
        <a href="{{ route('departments.index') }}" class="btn btn-light">Cancelar</a>
    </form>
</div>
@endsection
