@extends('layouts.app')
@section('content')
<h3>Dashboard (Manager)</h3>

<div class="row g-3">
  @foreach($stats as $k=>$v)
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <div class="display-6">{{ $v }}</div>
          <div class="text-muted text-uppercase small">{{ str_replace('_',' ', $k) }}</div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="card mt-3">
  <div class="card-header">Problemas más comunes (top categorías)</div>
  <div class="card-body">
    <ul class="mb-0">
      @forelse($commonIssues as $ci)
        <li>{{ $ci['category'] }} — {{ $ci['count'] }}</li>
      @empty
        <li>No hay datos aún</li>
      @endforelse
    </ul>
  </div>
</div>
@endsection
