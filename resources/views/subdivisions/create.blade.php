@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Nueva subdivisión - {{ $department->name }}</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="m-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('departments.subdivisions.store', $department) }}" class="card card-body">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre de la subdivisión</label>
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
            <label class="form-label">Encargado (DeptSupport)</label>
            <select name="agent_user_id" class="form-select @error('agent_user_id') is-invalid @enderror">
                <option value="">Sin encargado</option>
                @foreach($agents as $a)
                    <option value="{{ $a->id }}" @selected(old('agent_user_id') == $a->id)>
                        {{ $a->name }} ({{ $a->email }})
                    </option>
                @endforeach
            </select>
            @error('agent_user_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">
                Solo aparecen usuarios DeptSupport activos del departamento {{ $department->name }}.
            </small>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">Guardar</button>
            <a href="{{ route('departments.subdivisions.index', $department) }}" class="btn btn-light">Cancelar</a>
        </div>
    </form>
</div>
@endsection
