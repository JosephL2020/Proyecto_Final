@extends('layouts.app')

@section('content')
<h3 class="mb-3">Nuevo usuario</h3>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="m-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.store') }}" class="card card-body">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input
            name="name"
            value="{{ old('name') }}"
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
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input
            name="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            required
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
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
            <option value="Manager"     @selected(old('role') === 'Manager')>Gerente IT</option>
            <option value="IT"          @selected(old('role') === 'IT')>Soporte IT</option>
            <option value="DeptManager" @selected(old('role') === 'DeptManager')>Gerente de Departamento</option>
            <option value="DeptSupport" @selected(old('role') === 'DeptSupport')>Soporte de Departamento</option>
            <option value="Empleado"    @selected(old('role') === 'Empleado')>Empleado</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Departamento (se amarra el usuario a un departamento) --}}
    <div class="mb-3">
        <label class="form-label">Departamento</label>
        <select
            name="department_id"
            id="department_id"
            class="form-select @error('department_id') is-invalid @enderror"
        >
            <option value="">Seleccione un departamento...</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" @selected(old('department_id') == $d->id)>
                    {{ $d->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            Para Gerente de Departamento / Soporte / Empleado es obligatorio. Para Gerente IT puede quedar vacío si querés.
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
            @checked(old('can_manage_departments') == '1')
        >
        <label class="form-check-label" for="can_manage_departments">
            Permitir que este usuario IT administre Departamentos
        </label>
    </div>

    <div class="form-check mb-3">
        <input
            class="form-check-input"
            type="checkbox"
            name="is_active"
            id="is_active"
            value="1"
            @checked(old('is_active', true))
        >
        <label class="form-check-label" for="is_active">
            Usuario activo
        </label>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Guardar</button>
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

        // Solo IT ve el checkbox de "administrar departamentos"
        const isIT = role === 'IT';
        canWrap.style.display = isIT ? 'block' : 'none';
        if (!isIT && canChk) canChk.checked = false;

        // Departamento requerido para todos EXCEPTO Manager (Gerente IT)
        const needsDept = role !== '' && role !== 'Manager';
        deptSelect.required = needsDept;

        // Opcional: si es Manager, no obligar y permitir dejarlo vacío
        // Si querés obligar también al Manager, cambiá needsDept a: (role !== '')
    }

    roleSelect.addEventListener('change', applyRules);
    applyRules();
});
</script>
@endsection
