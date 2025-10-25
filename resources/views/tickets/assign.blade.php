@extends('layouts.app')
@section('content')
<h3>Asignar Ticket {{ $ticket->code }}</h3>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Asignar a</label>
        <select name="assigned_to" class="form-select" required>
          @foreach($its as $u)
            <option value="{{ $u->id }}" @selected($ticket->assigned_to==$u->id)>{{ $u->name }} ({{ $u->role }})</option>
          @endforeach
        </select>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary">Guardar</button>
        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
