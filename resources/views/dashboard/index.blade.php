@extends('layouts.app')
@section('content')
<h3 class="mb-3">Panel de Control</h3>

{{-- Resumen global --}}
<div class="row g-3 mb-3">
  <div class="col-auto"><span class="badge text-bg-secondary p-2">Total: {{ $stats['total'] ?? 0 }}</span></div>
  <div class="col-auto"><span class="badge text-bg-dark p-2">Abiertos: {{ $stats['open'] ?? 0 }}</span></div>
  <div class="col-auto"><span class="badge text-bg-info p-2">Asignados: {{ $stats['assigned'] ?? 0 }}</span></div>
  <div class="col-auto"><span class="badge text-bg-warning p-2">En progreso: {{ $stats['in_progress'] ?? 0 }}</span></div>
  <div class="col-auto"><span class="badge text-bg-success p-2">Resueltos: {{ $stats['resolved'] ?? 0 }}</span></div>
  <div class="col-auto"><span class="badge text-bg-secondary p-2">Cerrados: {{ $stats['closed'] ?? 0 }}</span></div>
  <div class="col-auto"><span class="badge text-bg-danger p-2">Cancelados: {{ $stats['cancelled'] ?? 0 }}</span></div>
</div>

<table class="table table-bordered align-middle">
  <thead class="table-light">
    <tr>
      <th>Soporte (IT)</th>
      <th>Abiertos</th>
      <th>Asignados</th>
      <th>En progreso</th>
      <th>Resueltos</th>
      <th>Cerrados</th>
      <th>Cancelados</th>
      <th>Promedio ★</th>
      <th># Calificados</th>
    </tr>
  </thead>
  <tbody>
    @forelse($its as $it)
      @php $s = $itStats->get($it->id); $avg = (float)($s->avg_rating ?? 0); @endphp
      <tr>
        <td>{{ $it->name }}</td>
        <td>{{ $s->open_cnt ?? 0 }}</td>
        <td>{{ $s->assigned_cnt ?? 0 }}</td>
        <td>{{ $s->in_progress_cnt ?? 0 }}</td>
        <td>{{ $s->resolved_cnt ?? 0 }}</td>
        <td>{{ $s->closed_cnt ?? 0 }}</td>
        <td>{{ $s->cancelled_cnt ?? 0 }}</td>
        <td>
          @if($avg > 0)
            <span class="me-1">{{ number_format($avg,2) }}</span>
            @for($i=1;$i<=5;$i++)
              <span class="text-warning">@if($i <= round($avg)) ★ @else ☆ @endif</span>
            @endfor
          @else
            —
          @endif
        </td>
        <td>{{ $s->rated_count ?? 0 }}</td>
      </tr>
    @empty
      <tr><td colspan="9" class="text-center text-muted">No hay usuarios IT.</td></tr>
    @endforelse
  </tbody>
</table>

@php
  // Usamos topCategories (Category::withCount('tickets')->take(5))
  $maxTickets = max(1, optional($topCategories)->max('tickets_count') ?? 1);
@endphp

<style>
  .cat-link { display:block; text-decoration:none; color:inherit; padding:.25rem .4rem; border-radius:.5rem; }
  .cat-link:hover { background:#f8fafc; }
</style>

<div class="card mt-4">
  <div class="card-header">Top categorías</div>
  <div class="card-body">
    @forelse($topCategories ?? [] as $c)
      @php
        $pct = $maxTickets ? ($c->tickets_count / $maxTickets * 100) : 0;
        $url = route('tickets.index', ['category_id' => $c->id]);
      @endphp
      <a href="{{ $url }}" class="cat-link mb-2">
        <div class="d-flex justify-content-between align-items-center">
          <strong>{{ $c->name }}</strong>
          <span class="text-muted">{{ $c->tickets_count }}</span>
        </div>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar" role="progressbar" style="width: {{ number_format($pct,2) }}%"></div>
        </div>
      </a>
    @empty
      <em class="text-muted">Sin datos</em>
    @endforelse
  </div>
</div>
@endsection
