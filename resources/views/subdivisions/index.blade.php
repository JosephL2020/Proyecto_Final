@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Subdivisiones</h3>
            <div class="text-muted">{{ $department->name }}</div>
        </div>
        <a href="{{ route('departments.subdivisions.create', $department) }}" class="btn btn-primary">Crear subdivisión</a>
    </div>

    @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Subdivisión</th>
                        <th>Encargado</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subdivisions as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->agent?->name ?? '-' }}</td>
                            <td>{{ $s->agent?->email ?? '-' }}</td>
                        </tr>
                    @endforeach
                    @if($subdivisions->count() === 0)
                        <tr><td colspan="3" class="text-center text-muted py-4">Sin subdivisiones registradas</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('departments.index') }}" class="btn btn-light">Volver</a>
    </div>
</div>
@endsection
