@extends('layouts.app')

@section('content')
<h3>Editar Ticket {{ $ticket->code }}</h3>

@php
    $isManager = auth()->user()->isManager();
@endphp

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
      @csrf
      @method('PUT')

      {{-- TÍTULO --}}
      <div class="mb-3">
        <label class="form-label">Título</label>
        <input name="title" class="form-control"
               value="{{ old('title', $ticket->title) }}"
               @if(!$isManager) disabled @endif required>
      </div>

      {{-- DESCRIPCIÓN --}}
      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="description" class="form-control" rows="5"
                  @if(!$isManager) disabled @endif required>{{ old('description', $ticket->description) }}</textarea>
      </div>

      <div class="row">
        {{-- CATEGORÍA --}}
        <div class="col-md-4 mb-3">
          <label class="form-label">Categoría</label>
          <select name="category_id" class="form-select" @if(!$isManager) disabled @endif>
            <option value="">—</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected(old('category_id', $ticket->category_id)==$c->id)>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- PRIORIDAD (solo Manager puede cambiarla) --}}
        <div class="col-md-4 mb-3">
          <label class="form-label">Prioridad</label>
          <select name="priority" class="form-select" @if(!$isManager) disabled @endif>
            @foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta'] as $val=>$label)
              <option value="{{ $val }}" @selected(old('priority', $ticket->priority)===$val)>
                {{ $label }}
              </option>
            @endforeach
          </select>

          @if(!$isManager)
            <input type="hidden" name="priority" value="{{ $ticket->priority }}">
          @endif
        </div>

        {{-- ESTADO --}}
        <div class="col-md-4 mb-3">
          <label class="form-label">Estado</label>

          @php
              $labels = [
                  'open'        => 'Abierto',
                  'assigned'    => 'Asignado',
                  'in_progress' => 'En progreso',
                  'resolved'    => 'Resuelto',
                  'closed'      => 'Cerrado',
                  'cancelled'   => 'Cancelado',
              ];
              $estadoActual = old('status', $ticket->status);
              $estados = $allowedStatuses ?? ['open','assigned','in_progress','resolved','closed','cancelled'];
          @endphp

          <select name="status" class="form-select" required>
              @foreach($estados as $st)
                  <option value="{{ $st }}" @selected($estadoActual === $st)>
                      {{ $labels[$st] }}
                  </option>
              @endforeach
          </select>
        </div>
      </div>

      {{-- BOTONES --}}
      <div class="d-flex gap-2 mt-2">
        <button class="btn btn-primary">Guardar cambios</button>
        <a class="btn btn-outline-secondary" href="{{ route('tickets.show', $ticket) }}">Cancelar</a>
      </div>

      {{-- Hidden values when limited --}}
      @if(!$isManager)
        <input type="hidden" name="title" value="{{ $ticket->title }}">
        <input type="hidden" name="description" value="{{ $ticket->description }}">
        <input type="hidden" name="category_id" value="{{ $ticket->category_id }}">
      @endif

    </form>
  </div>
</div>
@endsection
