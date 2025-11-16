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
      @can('delete', $ticket)
      <form method="POST" action="{{ route('tickets.destroy',$ticket) }}" onsubmit="return confirm('¿Eliminar ticket definitivamente?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Eliminar</button>
      </form>
      @endcan
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
  @php $prio = $ticket->priority; @endphp

  @if($prio)
      @php
          switch ($prio) {
              case 'high':   $label = 'Alta';  $cls = 'danger';    break;
              case 'medium': $label = 'Media'; $cls = 'warning';   break;
              case 'low':    $label = 'Baja';  $cls = 'secondary'; break;
              default:       $label = ucfirst($prio); $cls = 'secondary';
          }
      @endphp
      <span class="badge text-bg-{{ $cls }}">{{ $label }}</span>
  @else
      <span class="text-muted">Pendiente de definición por Gerencia de IT.</span>
  @endif
</p>

        <p class="text-muted">
          Estado: <strong>{{ ucfirst($ticket->status) }}</strong> |
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

  {{-- Sidebar derecha --}}
  <div class="col-lg-4">
    {{-- Recomendaciones del sistema --}}
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span>Recomendaciones del sistema</span>
        <form method="POST" action="{{ route('tickets.ai', $ticket) }}">
          @csrf
          <button class="btn btn-outline-primary btn-sm">Regenerar IA</button>
        </form>
      </div>
      <div class="card-body">
        @php
          $lista = isset($recs) && is_array($recs) ? $recs : ($ticket->aiSuggestion?->suggestions ?? []);
          if (is_string($lista)) { $tmp = json_decode($lista, true); if (json_last_error()===JSON_ERROR_NONE) $lista = $tmp; }
        @endphp
        {{-- Cambiar estado inline: visible solo para el IT asignado que NO es manager --}}
@if(auth()->id() === $ticket->assigned_to && !auth()->user()->isManager())
  <div class="card mb-3">
    <div class="card-header">Cambiar estado</div>
    <div class="card-body">
      <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="d-flex gap-2">
        @csrf
        @method('PUT')

        @php
          $labels = [
            'in_progress' => 'En progreso',
            'resolved'    => 'Resuelto',
            'cancelled'   => 'Cancelado',
          ];
        @endphp

        <select name="status" class="form-select" required style="max-width: 220px;">
          @foreach($labels as $val => $label)
            <option value="{{ $val }}" @selected($ticket->status === $val)>{{ $label }}</option>
          @endforeach
        </select>

        <button class="btn btn-primary">Actualizar estado</button>
      </form>
      <div class="form-text mt-2">
        Solo puedes cambiar a: En progreso, Resuelto o Cancelado.
      </div>
    </div>
  </div>
@endif


        @if(!empty($lista) && is_array($lista))
          <ol class="mb-0">
            @foreach($lista as $s)
              <li>{{ $s }}</li>
            @endforeach
          </ol>
        @else
          <em>No hay recomendaciones disponibles</em>
        @endif
      </div>
    </div>

    {{-- Evaluación del servicio (debajo de recomendaciones) --}}
    <div class="card">
      <div class="card-header">Evaluación del servicio</div>
      <div class="card-body">
        @if($ticket->rating)
          <div class="mb-2">
            @for($i=1;$i<=5;$i++)
              <span class="fs-5 text-warning">@if($i <= $ticket->rating) ★ @else ☆ @endif</span>
            @endfor
            <span class="ms-2">({{ $ticket->rating }}/5)</span>
          </div>
          @if($ticket->rating_comment)
            <div class="small text-muted mb-1">Comentario del usuario:</div>
            <div class="border rounded p-2">{{ $ticket->rating_comment }}</div>
          @endif
        @elseif(auth()->id() === $ticket->created_by)
          <form method="POST" action="{{ route('tickets.rate', $ticket) }}">
            @csrf
            <div class="mb-2">Selecciona tu calificación:</div>
            <div class="d-flex align-items-center gap-2 mb-3">
              {{-- Radios 1-5 como estrellas --}}
              @for($i=1;$i<=5;$i++)
                <label class="fs-4" style="cursor:pointer;">
                  <input type="radio" name="rating" value="{{ $i }}" class="d-none" required
                         onchange="this.closest('label').parentElement.querySelectorAll('span').forEach((n,idx)=>n.textContent = (idx < {{ $i }}) ? '★' : '☆');">
                  <span>☆</span>
                </label>
              @endfor
            </div>

            <div class="mb-3">
              <label class="form-label">Comentario (opcional)</label>
              <textarea name="rating_comment" class="form-control" rows="3" placeholder="¿Qué tal fue la atención?"></textarea>
            </div>

            <button class="btn btn-primary w-100">Enviar evaluación</button>
          </form>
        @else
          <em class="text-muted">Aún no ha sido evaluado.</em>
        @endif

        @can('update', $ticket)
  <a class="btn btn-outline-primary" href="{{ route('tickets.edit',$ticket) }}">Editar</a>
@endcan

      </div>
    </div>
  </div>
</div>
@endsection
