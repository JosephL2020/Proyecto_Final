@extends('layouts.app')
@section('content')
<h3>Editar Ticket {{ $ticket->code }}</h3>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Título</label>
        <input name="title" class="form-control" value="{{ old('title', $ticket->title) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="description" class="form-control" rows="5" required>{{ old('description', $ticket->description) }}</textarea>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Categoría</label>
          <select name="category_id" class="form-select">
            <option value="">-</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected(old('category_id', $ticket->category_id)==$c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Prioridad</label>
          <select name="priority" class="form-select" required>
            @foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta'] as $val=>$label)
              <option value="{{ $val }}" @selected(old('priority', $ticket->priority)===$val)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Estado</label>
          <select name="status" class="form-select" required>
            @foreach(['open','assigned','in_progress','resolved','closed','cancelled'] as $st)
              <option value="{{ $st }}" @selected(old('status', $ticket->status)===$st)>{{ $st }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary">Guardar cambios</button>
        <a class="btn btn-outline-secondary" href="{{ route('tickets.show', $ticket) }}">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
