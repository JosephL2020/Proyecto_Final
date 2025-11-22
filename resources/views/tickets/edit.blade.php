@extends('layouts.app')

@section('content')
<style>
    .page-title {
        font-size: 1.15rem;
        font-weight: 600;
    }
    .page-subtitle {
        font-size: .85rem;
    }
    .ticket-code-pill {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .1rem .45rem;
        border-radius: 999px;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        font-size: .75rem;
        font-weight: 500;
        color: #111827;
    }
    .form-section-title {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: .4rem;
    }
    body.dark-mode .ticket-code-pill {
        background-color: #0b1120 !important;
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }
</style>

@php
    $statusOptions = [
        'open'        => 'Abierto',
        'assigned'    => 'Asignado',
        'in_progress' => 'En progreso',
        'resolved'    => 'Resuelto',
        'closed'      => 'Cerrado',
        'cancelled'   => 'Cancelado',
    ];

    $priorityOptions = [
        'high'   => 'Alta',
        'medium' => 'Media',
        'low'    => 'Baja',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="page-title">
            Editar ticket
        </div>
        <div class="page-subtitle text-muted">
            Modifica el estado general, prioridad y detalles del ticket seleccionado.
        </div>
    </div>
    <div>
        <span class="ticket-code-pill">
            {{ $ticket->code ?? 'SIN CÓDIGO' }}
        </span>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Hay errores en el formulario:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('tickets.update', $ticket) }}">
    @csrf
    @method('PUT')

    <div class="row g-3">

        {{-- Columna izquierda: información principal --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">

                    <div class="form-section-title">Información del ticket</div>

                    {{-- Título --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $ticket->title) }}"
                            required
                        >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Describe el problema o solicitud del ticket"
                        >{{ old('description', $ticket->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- Columna derecha: estado, prioridad, asignación --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">

                    <div class="form-section-title">Estado general</div>

                    {{-- Estado --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select
                            id="status"
                            name="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('status', $ticket->status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Prioridad --}}
                    <div class="mb-3">
                        <label for="priority" class="form-label">Prioridad</label>
                        <select
                            id="priority"
                            name="priority"
                            class="form-select @error('priority') is-invalid @enderror"
                            required
                        >
                            @foreach($priorityOptions as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('priority', $ticket->priority) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Asignado a (solo si el controlador manda $its) --}}
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Asignado a</label>

                        @if(isset($its) && count($its))
                            <select
                                id="assigned_to"
                                name="assigned_to"
                                class="form-select @error('assigned_to') is-invalid @enderror"
                            >
                                <option value="">Sin asignar</option>
                                @foreach($its as $user)
                                    <option value="{{ $user->id }}"
                                        @selected(old('assigned_to', $ticket->assigned_to) == $user->id)>
                                        {{ $user->name }} ({{ $user->role }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @else
                            <p class="text-muted small mb-0">
                                No se recibió la lista de usuarios IT para asignar desde el controlador.
                            </p>
                        @endif
                    </div>

                    {{-- Info de creación --}}
                    <div class="mt-3 small text-muted">
                        <div><strong>Creado:</strong>
                            {{ optional($ticket->created_at)->format('Y-m-d H:i') ?? 'N/D' }}
                        </div>
                        <div><strong>Última actualización:</strong>
                            {{ optional($ticket->updated_at)->format('Y-m-d H:i') ?? 'N/D' }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Botones --}}
    <div class="d-flex justify-content-between mt-2">
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary">
            Guardar cambios
        </button>
    </div>

</form>
@endsection
