@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Crear nuevo ticket</h3>

    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Departamento --}}
        <div class="mb-3">
            <label class="form-label">Departamento</label>
            <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                <option value="">Seleccione un departamento...</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Subdivisión --}}
        <div class="mb-3">
            <label class="form-label">Subdivisión / Área</label>
            <select id="subdivision_id" name="subdivision_id" class="form-select @error('subdivision_id') is-invalid @enderror" required disabled>
                <option value="">Primero seleccione un departamento...</option>
            </select>
            @error('subdivision_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Título --}}
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Descripción --}}
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Categoría --}}
        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                <option value="">-</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Adjuntos opcionales --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Adjuntar archivos</label>
            <input type="file"
                   name="attachments[]"
                   multiple
                   class="form-control form-control-sm @error('attachments.*') is-invalid @enderror"
                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
            @error('attachments.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">
                Puedes adjuntar imágenes, PDFs o documentos (máx. 5 MB por archivo).
            </small>
        </div>

        <p class="text-muted mb-3">
            Prioridad: <strong>Pendiente de definición por Gerencia de IT</strong>.
        </p>

        <button class="btn btn-primary">Crear</button>
        <a href="{{ route('tickets.index') }}" class="btn btn-light">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deptSelect = document.getElementById('department_id');
    const subSelect  = document.getElementById('subdivision_id');

    async function loadSubdivisions(deptId, oldSubId = null) {
        subSelect.innerHTML = '<option value="">Cargando...</option>';
        subSelect.disabled = true;

        if (!deptId) {
            subSelect.innerHTML = '<option value="">Primero seleccione un departamento...</option>';
            return;
        }

        try {
            const url = `/departments/${deptId}/subdivisions/options`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
            const data = await res.json();

            subSelect.innerHTML = '<option value="">Seleccione una subdivisión...</option>';

            data.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                if (oldSubId && String(oldSubId) === String(s.id)) opt.selected = true;
                subSelect.appendChild(opt);
            });

            subSelect.disabled = false;

        } catch (e) {
            subSelect.innerHTML = '<option value="">Error cargando subdivisiones</option>';
        }
    }

    deptSelect.addEventListener('change', () => loadSubdivisions(deptSelect.value));

    // Si viene old() (por validación fallida), recargar
    const oldDept = "{{ old('department_id') }}";
    const oldSub  = "{{ old('subdivision_id') }}";
    if (oldDept) loadSubdivisions(oldDept, oldSub);
});
</script>
@endsection
