@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <h3 class="m-0">Listado de Tickets</h3>
  <a href="{{ route('tickets.create') }}" class="btn btn-primary">Nuevo Ticket</a>
</div>

<form method="GET" class="mb-3">
  <div class="input-group">
    <input type="text" name="s" class="form-control" placeholder="Buscar por título o descripción" value="{{ request('s') }}">
    <button class="btn btn-secondary">Buscar</button>
  </div>
</form>

<table class="table table-bordered table-hover">
  <thead class="table-light">
    <tr>
      <th>Código</th>
      <th>Título</th>
      <th>Prioridad</th>
      <th>Estado</th>
      <th>Asignado a</th>
      <th>Fecha</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($tickets as $ticket)
      <tr>
        <td>{{ $ticket->code }}</td>
        <td>{{ $ticket->title }}</td>
        <td>
          <span class="badge text-bg-{{ $ticket->priority=='high'?'danger':($ticket->priority=='medium'?'warning':'secondary') }}">
            {{ $ticket->priority }}
          </span>
        </td>
        <td>{{ $ticket->status }}</td>
        <td>{{ $ticket->assignee->name ?? 'Sin asignar' }}</td>
        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
        <td><a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">Ver</a></td>
      </tr>
    @endforeach
  </tbody>
</table>

{{ $tickets->links() }}
@endsection
