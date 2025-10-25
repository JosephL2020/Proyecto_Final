@extends('layouts.app')
@section('content')
<h3>Crear Nuevo Ticket</h3>
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
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Categoría</label>
      <select name="category_id" class="form-select">
        <option value="">Seleccione</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Prioridad</label>
      <select name="priority" class="form-select" required>
        <option value="low">Baja</option>
        <option value="medium" selected>Media</option>
        <option value="high">Alta</option>
      </select>
    </div>
  </div>
  <button class="btn btn-success">Guardar Ticket</button>
</form>
@endsection
