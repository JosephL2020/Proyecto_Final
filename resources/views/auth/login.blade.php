<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Gestión de Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-dark: #0a2463;
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --accent: #60a5fa;
            --text-light: #f8fafc;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --input-bg: #334155;
            --border-color: #475569;
        }
        
        body {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--primary-dark) 100%);
            color: var(--text-light);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .login-card {
            background-color: var(--card-bg);
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .card-header {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            border-bottom: none;
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .logo i {
            font-size: 2rem;
            color: white;
        }
        
        .system-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 2rem;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            margin-bottom: 1rem;
            display: inline-block;
        }
        
        .card-body {
            padding: 2rem 1.5rem;
        }
        
        .form-label {
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
        }
        
        .input-group-text {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-right: none;
            color: var(--accent);
        }
        
        .form-control {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus {
            background-color: var(--input-bg);
            border-color: var(--accent);
            color: var(--text-light);
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25);
        }
        
        .form-control::placeholder {
            color: #94a3b8;
        }
        
        .btn-primary {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }
        
        .btn-outline-secondary {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-left: none;
            color: var(--accent);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }
        
        .form-check-label {
            color: var(--text-light);
        }
        
        .forgot-password {
            color: var(--accent);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .forgot-password:hover {
            color: var(--primary-light);
        }
        
        .footer-text {
            text-align: center;
            color: #94a3b8;
            font-size: 0.875rem;
            margin-top: 1.5rem;
        }
        
        /* Efectos de animación */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <div class="logo-container">
                    <div class="logo">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                </div>
                <span class="system-badge">Sistema de Gestión de Tickets</span>
                <h5 class="mb-1 text-white">Iniciar sesión</h5>
                <p class="mb-0 small text-white-50">
                    Accede para gestionar y dar seguimiento a los tickets de soporte.
                </p>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
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
                    
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
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
                    
                    <!-- Recordarme + Olvidé contraseña -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
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
                            <a class="small forgot-password" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                    
                    <!-- Botón de login -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary" id="login-btn">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            <span class="btn-text">Entrar</span>
                            <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </button>
                    </div>
                </form>
                
                <div class="footer-text">
                    &copy; {{ date('Y') }} Sistema de Gestión de Tickets. Todos los derechos reservados.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para mostrar/ocultar contraseña
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
            
            // Script para el botón de login
            document.querySelector('form').addEventListener('submit', function() {
                const btn = document.getElementById('login-btn');
                btn.disabled = true;
                btn.querySelector('.btn-text').textContent = 'Entrando...';
                btn.querySelector('.spinner-border').classList.remove('d-none');
            });
        });
    </script>
</body>
</html>