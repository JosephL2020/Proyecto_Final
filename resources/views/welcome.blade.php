<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Tickets</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            },
            darkMode: 'class'
        }
    </script>
</head>

<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-6xl w-full">
        
        <!-- Header Minimalista -->
        <header class="flex justify-between items-center mb-16 lg:mb-20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="bi bi-ticket-perforated text-white text-lg"></i>
                </div>
                <span class="text-xl font-semibold">TicketFlow</span>
            </div>


        </header>

        <!-- Hero Section -->
        <main class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            
            <!-- Contenido Texto -->
            <div class="space-y-8 animate-fade-in-up">
                <div class="space-y-4">
                    <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                        Gestión de 
                        <span class="text-blue-600 block">Soporte IT</span>
                    </h1>
                    
                    <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                        Sistema profesional para gestión de tickets de soporte técnico. 
                        Centraliza, prioriza y resuelve incidencias de manera eficiente.
                    </p>
                </div>

                <!-- Características Minimalistas -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-check-lg text-green-600 text-xs"></i>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Flujo de trabajo optimizado</span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-check-lg text-blue-600 text-xs"></i>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Tiempos de respuesta rápidos</span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-check-lg text-purple-600 text-xs"></i>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Interfaz intuitiva y limpia</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="bi bi-box-arrow-in-right mr-3"></i>
                        Acceder al Sistema
                    </a>
                </div>

                
            </div>

            <!-- Visual Section  -->
            <div class="relative animate-float">
                <div class="relative bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-2xl p-8 lg:p-12 shadow-xl">
                    
                    <!-- Card de ejemplo de ticket -->
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 max-w-xs mx-auto transform rotate-2">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-blue-600">TICKET #001</span>
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs rounded-full">Abierto</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Problema con acceso al sistema</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Usuario reporta problemas para acceder al dashboard principal...</p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>Hace 2h</span>
                            <span>Prioridad: Media</span>
                        </div>
                    </div>

                    <!-- Elementos decorativos sutiles -->
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-blue-200 dark:bg-blue-800 rounded-full opacity-50"></div>
                    <div class="absolute -bottom-6 -left-6 w-12 h-12 bg-indigo-200 dark:bg-indigo-800 rounded-full opacity-30"></div>
                </div>
            </div>
        </main>

        <!-- Footer Minimalista -->
        <footer class="mt-20 lg:mt-24 text-center border-t border-gray-200 dark:border-gray-800 pt-8">
            <p class="text-sm text-gray-500 dark:text-gray-600">
                &copy; {{ date('Y') }} TicketFlow. Sistema de gestión de soporte técnico.
            </p>
        </footer>
    </div>

    <!-- Script para dark mode toggle (opcional) -->
    <script>
        // Si quieres agregar toggle de dark mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</body>
</html>