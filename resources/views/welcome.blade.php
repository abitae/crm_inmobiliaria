<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Open9 CRM Inmobiliaria - La solución completa para gestionar tu negocio inmobiliario">

        <title>Open9 CRM Inmobiliaria - Gestión Profesional de Propiedades</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            'inter': ['Inter', 'sans-serif'],
                        },
                        colors: {
                            'primary': {
                                50: '#eff6ff',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                900: '#1e3a8a',
                            }
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="font-inter bg-gradient-to-br from-slate-50 to-slate-200 dark:from-slate-900 dark:to-slate-800 text-slate-800 dark:text-slate-200 min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/50 dark:border-slate-700/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex justify-between items-center py-4">
                    <a href="#" class="flex items-center gap-3 text-2xl font-bold text-primary-600 dark:text-primary-400">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white font-bold text-lg">9</div>
                        Open9 CRM
                    </a>
                    <div class="hidden md:flex items-center gap-8">
                        <a href="#features" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors">Características</a>
                        <a href="#pricing" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors">Precios</a>
                        <a href="#contact" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors">Contacto</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-semibold rounded-lg hover:bg-primary-600 hover:text-white transition-all duration-200">Iniciar Sesión</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">Registrarse</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero Section -->
            <section class="py-20 px-4">
                <div class="max-w-7xl mx-auto text-center">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 dark:from-slate-100 dark:via-slate-200 dark:to-slate-300 bg-clip-text text-transparent leading-tight">
                        Gestiona tu Negocio Inmobiliario con Open9 CRM
                    </h1>
                    <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-300 mb-8 max-w-3xl mx-auto leading-relaxed">
                        La plataforma completa para agentes inmobiliarios que quieren vender más propiedades, gestionar clientes eficientemente y hacer crecer su negocio.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="#demo" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-200 text-lg">
                            Solicitar Demo
                        </a>
                        <a href="#features" class="inline-flex items-center gap-2 px-8 py-4 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-semibold rounded-lg hover:bg-primary-600 hover:text-white transition-all duration-200 text-lg">
                            Ver Características
                        </a>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="py-20 bg-white dark:bg-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4">
                            Todo lo que necesitas en un CRM Inmobiliario
                        </h2>
                        <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                            Herramientas poderosas diseñadas específicamente para el sector inmobiliario
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="bg-slate-50 dark:bg-slate-700 p-8 rounded-2xl border border-slate-200 dark:border-slate-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white text-2xl mb-6">🏠</div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Gestión de Propiedades</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                Administra tu inventario de propiedades con fotos, descripciones, precios y estado de disponibilidad en tiempo real.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="bg-slate-50 dark:bg-slate-700 p-8 rounded-2xl border border-slate-200 dark:border-slate-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white text-2xl mb-6">👥</div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Gestión de Clientes</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                Mantén un historial completo de cada cliente, sus preferencias, visitas y comunicaciones para cerrar más ventas.
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="bg-slate-50 dark:bg-slate-700 p-8 rounded-2xl border border-slate-200 dark:border-slate-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white text-2xl mb-6">📊</div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Reportes y Analytics</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                Analiza el rendimiento de tu negocio con reportes detallados sobre ventas, clientes y propiedades más populares.
                            </p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="bg-slate-50 dark:bg-slate-700 p-8 rounded-2xl border border-slate-200 dark:border-slate-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white text-2xl mb-6">📱</div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Acceso Móvil</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                Gestiona tu negocio desde cualquier lugar con nuestra aplicación móvil optimizada para agentes inmobiliarios.
                            </p>
                        </div>

                        <!-- Feature 5 -->
                        <div class="bg-slate-50 dark:bg-slate-700 p-8 rounded-2xl border border-slate-200 dark:border-slate-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white text-2xl mb-6">🔔</div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Notificaciones Inteligentes</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                Recibe alertas automáticas sobre nuevas oportunidades, recordatorios de seguimiento y actualizaciones importantes.
                            </p>
                        </div>

                        <!-- Feature 6 -->
                        <div class="bg-slate-50 dark:bg-slate-700 p-8 rounded-2xl border border-slate-200 dark:border-slate-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white text-2xl mb-6">💼</div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Gestión de Tareas</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                Organiza tu agenda con tareas, recordatorios y seguimientos para nunca perder una oportunidad de venta.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        <div class="space-y-2">
                            <h3 class="text-4xl md:text-5xl font-bold">500+</h3>
                            <p class="text-lg opacity-90">Agentes Inmobiliarios</p>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-4xl md:text-5xl font-bold">10,000+</h3>
                            <p class="text-lg opacity-90">Propiedades Gestionadas</p>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-4xl md:text-5xl font-bold">98%</h3>
                            <p class="text-lg opacity-90">Satisfacción del Cliente</p>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-4xl md:text-5xl font-bold">24/7</h3>
                            <p class="text-lg opacity-90">Soporte Técnico</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section id="demo" class="py-20 bg-slate-50 dark:bg-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4">
                        ¿Listo para Transformar tu Negocio?
                    </h2>
                    <p class="text-xl text-slate-600 dark:text-slate-300 mb-8 max-w-2xl mx-auto">
                        Únete a cientos de agentes inmobiliarios que ya están usando Open9 CRM para vender más propiedades y hacer crecer sus ingresos.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="#contact" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-200 text-lg">
                            Contactar Ventas
                        </a>
                        <a href="#features" class="inline-flex items-center gap-2 px-8 py-4 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-semibold rounded-lg hover:bg-primary-600 hover:text-white transition-all duration-200 text-lg">
                            Más Información
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 dark:bg-slate-950 text-slate-400 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; 2024 Open9 CRM Inmobiliaria. Todos los derechos reservados.</p>
            </div>
        </footer>
    </body>
</html>
