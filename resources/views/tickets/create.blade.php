@extends('layouts.app')
@section('content')
<h3>Nuevo Ticket</h3>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('tickets.store') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Título</label>
        <input name="title" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="description" class="form-control" rows="5" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Categoría</label>
        <select name="category_id" class="form-select">
          <option value="">-</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <p class="text-muted mb-3">
        Prioridad: <strong>Pendiente de definición por Gerencia de IT</strong>.
      </p>

      <button class="btn btn-primary">Crear</button>
      <a href="{{ route('tickets.index') }}" class="btn btn-light">Cancelar</a>
    </form>
  </div>
</div>
@endsection
