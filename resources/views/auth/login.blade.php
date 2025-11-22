@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">

        <div class="card shadow-sm border-0" style="border-radius: 1rem; margin-top: 1.5rem; margin-bottom: 2rem;">
            <div class="card-body p-4">

                <div class="text-center mb-3">
                    <div class="mb-2">
                        <span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1">
                            Sistema de Gestión de Tickets
                        </span>
                    </div>
                    <h5 class="mb-1">Iniciar sesión</h5>
                    <p class="text-muted mb-0 small">
                        Accede para gestionar y dar seguimiento a los tickets de soporte.
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label small text-muted">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input id="email"
                                   type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   autofocus
                                   placeholder="ejemplo@empresa.com">

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-2">
                        <label for="password" class="form-label small text-muted">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password"
                                   type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Ingresa tu contraseña">

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Recordarme + Olvidé contraseña --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="remember"
                                   id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">
                                Recordarme
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    {{-- Botón --}}
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Entrar
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
@endsection
