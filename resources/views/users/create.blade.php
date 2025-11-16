@extends('layouts.app')
@section('content')
<h3 class="mb-3">Nuevo usuario</h3>
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="m-0">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif
<form method="POST" action="{{ route('users.store') }}" class="card card-body">
  @csrf
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input name="name" value="{{ old('name') }}" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input name="email" type="email" value="{{ old('email') }}" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Contraseña</label>
    <input name="password" type="password" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Rol</label>
    <select name="role" class="form-select" required>
      @foreach($roles as $k=>$v)
        <option value="{{ $k }}" @selected(old('role')===$k)>{{ $v }}</option>
      @endforeach
    </select>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary">Guardar</button>
    <a class="btn btn-light" href="{{ route('users.index') }}">Cancelar</a>
  </div>
</form>
@endsection
