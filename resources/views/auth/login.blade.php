@extends('layouts.app')

@section('content')

{{-- CONTENEDOR COMPLETAMENTE CENTRADO --}}
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; padding-top: 2rem; padding-bottom: 2rem;">

    <div class="col-12 col-md-8 col-lg-5">

        <div class="card shadow-sm border-0" style="border-radius: 1rem;">
            <div class="card-body p-4">

                {{-- Encabezado --}}
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

                {{-- Formulario --}}
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
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Recordarme + Olvidé contraseña --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check small">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="remember"
                                   id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
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
                        <button type="submit" class="btn btn-primary" id="login-btn">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        <span class="btn-text">Entrar</span>
                        <div class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</div>


<script>
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.querySelector('.btn-text').textContent = 'Entrando...';
    btn.querySelector('.spinner-border').classList.remove('d-none');
});
</script>
            {{-- JavaScript para mostrar/ocultar contraseña --}}
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.getElementById('toggle-password');
                const passwordInput = document.getElementById('password');
                const passwordIcon = togglePassword.querySelector('i');
                
                togglePassword.addEventListener('click', function() {
                    // Cambiar el tipo de input
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Cambiar el ícono
                    if (type === 'text') {
                        passwordIcon.classList.remove('bi-eye');
                        passwordIcon.classList.add('bi-eye-slash');
                    } else {
                        passwordIcon.classList.remove('bi-eye-slash');
                        passwordIcon.classList.add('bi-eye');
                    }
                });
            });
            </script>



@endsection
