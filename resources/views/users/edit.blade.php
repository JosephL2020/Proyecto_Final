@extends('layouts.app')

@section('content')
<h3 class="mb-3">Editar usuario</h3>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="m-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.update', $user) }}" class="card card-body">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input
            name="name"
            value="{{ old('name', $user->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input
            name="email"
            type="email"
            value="{{ old('email', $user->email) }}"
            class="form-control @error('email') is-invalid @enderror"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <hr class="my-3">

    <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input
            name="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="Dejar en blanco para no cambiar"
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">
            Si vas a cambiar contraseña, en el backend estamos usando confirmed. Si querés confirmación en UI, te lo agrego también.
        </small>
    </div>

    <div class="mb-3">
        <label class="form-label">Rol</label>
        <select
            name="role"
            id="role"
            class="form-select @error('role') is-invalid @enderror"
            required
        >
            <option value="">Seleccione un rol...</option>
            <option value="Manager"     @selected(old('role', $user->role) === 'Manager')>Gerente IT</option>
            <option value="IT"          @selected(old('role', $user->role) === 'IT')>Soporte IT</option>
            <option value="DeptManager" @selected(old('role', $user->role) === 'DeptManager')>Gerente de Departamento</option>
            <option value="DeptSupport" @selected(old('role', $user->role) === 'DeptSupport')>Soporte de Departamento</option>
            <option value="Empleado"    @selected(old('role', $user->role) === 'Empleado')>Empleado</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Departamento --}}
    <div class="mb-3">
        <label class="form-label">Departamento</label>
        <select
            name="department_id"
            id="department_id"
            class="form-select @error('department_id') is-invalid @enderror"
        >
            <option value="">Seleccione un departamento...</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" @selected(old('department_id', $user->department_id) == $d->id)>
                    {{ $d->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            Para Gerente de Departamento / Soporte / Empleado es obligatorio. Para Gerente IT puede quedar vacío.
        </small>
    </div>

    {{-- Checkbox: permitir administrar departamentos (solo para IT) --}}
    <div class="form-check mb-3" id="canManageWrap" style="display:none;">
        <input
            class="form-check-input"
            type="checkbox"
            name="can_manage_departments"
            id="can_manage_departments"
            value="1"
            @checked(old('can_manage_departments', $user->can_manage_departments) == 1)
        >
        <label class="form-check-label" for="can_manage_departments">
            Permitir que este usuario IT administre Departamentos
        </label>
    </div>

    <div class="form-check mb-3">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            class="form-check-input"
            value="1"
            @checked(old('is_active', $user->is_active))
        >
        <label class="form-check-label" for="is_active">
            Usuario activo
        </label>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Actualizar</button>
        <a class="btn btn-light" href="{{ route('users.index') }}">Cancelar</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const deptSelect = document.getElementById('department_id');
    const canWrap = document.getElementById('canManageWrap');
    const canChk = document.getElementById('can_manage_departments');

    function applyRules() {
        const role = roleSelect.value;

        // Solo IT ve el checkbox
        const isIT = role === 'IT';
        canWrap.style.display = isIT ? 'block' : 'none';
        if (!isIT && canChk) canChk.checked = false;

        // Departamento requerido para todos EXCEPTO Manager
        const needsDept = role !== '' && role !== 'Manager';
        deptSelect.required = needsDept;
    }

    roleSelect.addEventListener('change', applyRules);
    applyRules();
});
</script>
@endsection
