@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Ticket {{ $ticket->code }}</h3>
  <div class="d-flex gap-2">
    @can('assign', $ticket)
      <a class="btn btn-outline-secondary" href="{{ route('tickets.assign.form',$ticket) }}">Asignar</a>
    @endcan
    @can('update', $ticket)
      <a class="btn btn-outline-primary" href="{{ route('tickets.edit',$ticket) }}">Editar</a>
      <form method="POST" action="{{ route('tickets.destroy',$ticket) }}" onsubmit="return confirm('¿Eliminar ticket definitivamente?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Eliminar</button>
      </form>
    @endcan
    <a class="btn btn-light" href="{{ route('tickets.index') }}">Volver</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{ $ticket->title }}</h5>
        <p class="text-muted mb-1">
          Categoría: {{ optional($ticket->category)->name ?: '-' }} |
          Prioridad:
          <span class="badge text-bg-{{ $ticket->priority=='high'?'danger':($ticket->priority=='medium'?'warning':'secondary') }}">
            {{ $ticket->priority }}
          </span>
        </p>
        <p class="text-muted">
          Estado: <strong>{{ $ticket->status }}</strong> |
          Creador: {{ $ticket->creator->name }} |
          Asignado: {{ optional($ticket->assignee)->name ?: '—' }}
        </p>
        <hr>
        <p>{{ $ticket->description }}</p>
      </div>
    </div>

    {{-- Comentarios --}}
    <div class="card mt-3">
      <div class="card-header">Comentarios</div>
      <div class="card-body">
        @forelse($ticket->comments as $c)
          <div class="mb-3">
            <div class="small text-muted">{{ $c->user->name }} • {{ $c->created_at->diffForHumans() }}</div>
            <div>{{ $c->body }}</div>
          </div>
          @if(!$loop->last)<hr>@endif
        @empty
          <em class="text-muted">Aún no hay comentarios.</em>
        @endforelse

        <form class="mt-3" method="POST" action="{{ route('tickets.comments.store',$ticket) }}">
          @csrf
          <div class="input-group">
            <input name="body" class="form-control" placeholder="Escribe un comentario..." required>
            <button class="btn btn-primary">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Recomendaciones del sistema --}}
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">Recomendaciones del sistema</div>
      <div class="card-body">
        @if($ticket->aiSuggestion)
          <ol class="mb-0">
            @foreach($ticket->aiSuggestion->suggestions as $s)
              <li>{{ $s }}</li>
            @endforeach
          </ol>
        @else
          <em>No hay recomendaciones disponibles</em>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
