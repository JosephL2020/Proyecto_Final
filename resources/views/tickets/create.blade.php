@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Crear nuevo ticket</h3>

    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
        @csrf

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
@endsection
