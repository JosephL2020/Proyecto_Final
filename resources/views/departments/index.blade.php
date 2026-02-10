@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Departamentos</h3>
        <a href="{{ route('departments.create') }}" class="btn btn-primary">Crear departamento</a>
    </div>

    @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Gerente</th>
                        <th>Correo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $d)
                        <tr>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->manager?->name ?? '-' }}</td>
                            <td>{{ $d->manager?->email ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('departments.edit', $d) }}" class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <a href="{{ route('departments.subdivisions.index', $d) }}" class="btn btn-sm btn-outline-secondary">
                                    Subdivisiones
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $departments->links() }}
    </div>
</div>
@endsection
