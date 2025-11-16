@extends('layouts.app')
@section('content')
<h3 class="mb-3">Editar usuario</h3>
@if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="m-0">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif
<form method="POST" action="{{ route('users.update',$user) }}" class="card card-body">
  @csrf @method('PUT')
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input name="name" value="{{ old('name',$user->name) }}" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input name="email" type="email" value="{{ old('email',$user->email) }}" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Nueva contraseña</label>
    <input name="password" type="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
  </div>
  <div class="mb-3">
    <label class="form-label">Rol</label>
    <select name="role" class="form-select" required>
      @foreach($roles as $k=>$v)
        <option value="{{ $k }}" @selected(old('role',$user->role)===$k)>{{ $v }}</option>
      @endforeach
    </select>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary">Actualizar</button>
    <a class="btn btn-light" href="{{ route('users.index') }}">Cancelar</a>
  </div>
</form>
@endsection
