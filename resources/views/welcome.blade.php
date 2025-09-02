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
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            'inter': ['Inter', 'sans-serif'],
                            'poppins': ['Poppins', 'sans-serif'],
                        },
                        colors: {
                            'primary': {
                                50: '#eff6ff',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                900: '#1e3a8a',
                            },
                            'accent': {
                                50: '#fef3c7',
                                500: '#f59e0b',
                                600: '#d97706',
                                700: '#b45309',
                            }
                        },
                        animation: {
                            'float': 'float 6s ease-in-out infinite',
                            'bounce-slow': 'bounce 3s infinite',
                            'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        },
                        keyframes: {
                            float: {
                                '0%, 100%': { transform: 'translateY(0px)' },
                                '50%': { transform: 'translateY(-20px)' },
                            }
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="font-poppins bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-700 text-slate-800 dark:text-slate-200 min-h-screen overflow-x-hidden">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/50 dark:border-slate-700/50 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex justify-between items-center py-4">
                    <a href="#" class="flex items-center gap-3 text-2xl font-bold text-primary-600 dark:text-primary-400 hover:scale-105 transition-transform">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-600 via-primary-500 to-accent-500 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg animate-pulse-slow">9</div>
                        <span class="bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">Open9 CRM</span>
                    </a>
                    <div class="hidden md:flex items-center gap-8">
                        <a href="#features" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-all duration-200 hover:scale-105">Características</a>
                        <a href="#pricing" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-all duration-200 hover:scale-105">Precios</a>
                        <a href="#contact" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-all duration-200 hover:scale-105">Contacto</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-500 text-white font-semibold rounded-xl hover:shadow-xl hover:-translate-y-1 transition-all duration-300 shadow-lg">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-semibold rounded-xl hover:bg-primary-600 hover:text-white transition-all duration-300 hover:scale-105">Iniciar Sesión</a>
                            @endauth
                        @endif
                    </div>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero Section -->
            <section class="relative py-24 px-4 overflow-hidden">
                <!-- Background Elements -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-primary-200/30 to-accent-200/30 rounded-full blur-3xl animate-float"></div>
                    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-blue-200/20 to-indigo-200/20 rounded-full blur-3xl animate-float" style="animation-delay: -3s;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto text-center relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary-100 to-accent-100 dark:from-primary-900/30 dark:to-accent-900/30 rounded-full text-primary-700 dark:text-primary-300 font-medium mb-6 animate-bounce-slow">
                        🚀 La plataforma líder en CRM Inmobiliario
                    </div>
                    
                    <h1 class="text-5xl md:text-7xl font-bold mb-8 bg-gradient-to-r from-slate-900 via-primary-700 to-accent-600 dark:from-slate-100 dark:via-primary-300 dark:to-accent-400 bg-clip-text text-transparent leading-tight">
                        Gestiona tu Negocio Inmobiliario con <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-500">Open9 CRM</span>
                    </h1>
                    
                    <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-300 mb-10 max-w-4xl mx-auto leading-relaxed">
                        La plataforma completa para agentes inmobiliarios que quieren <span class="font-semibold text-primary-600 dark:text-primary-400">vender más propiedades</span>, gestionar clientes eficientemente y hacer crecer su negocio de manera exponencial.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
                        <a href="#demo" class="group inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-primary-600 to-accent-500 text-white font-bold rounded-2xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-lg shadow-lg">
                            <span>Solicitar Demo</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        <a href="#features" class="group inline-flex items-center gap-3 px-10 py-5 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-bold rounded-2xl hover:bg-primary-600 hover:text-white transition-all duration-300 text-lg hover:scale-105">
                            <span>Ver Características</span>
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap justify-center items-center gap-8 text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Sin costos ocultos</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Implementación en 24h</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Soporte 24/7</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="relative py-24 bg-white dark:bg-slate-800 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #3b82f6 1px, transparent 0); background-size: 40px 40px;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-20">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-accent-100 to-primary-100 dark:from-accent-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-medium mb-6">
                            ✨ Características Principales
                        </div>
                        <h2 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6">
                            Todo lo que necesitas en un <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-500">CRM Inmobiliario</span>
                        </h2>
                        <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto leading-relaxed">
                            Herramientas poderosas diseñadas específicamente para el sector inmobiliario que transformarán tu forma de trabajar
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border border-slate-200/50 dark:border-slate-600/50 hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 hover:border-primary-200 dark:hover:border-primary-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center text-white text-3xl mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">🏠</div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Gestión de Propiedades</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                                Administra tu inventario de propiedades con fotos, descripciones, precios y estado de disponibilidad en tiempo real.
                            </p>
                            <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-full bg-gradient-to-r from-primary-500 to-accent-500 h-1 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border border-slate-200/50 dark:border-slate-600/50 hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 hover:border-primary-200 dark:hover:border-primary-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center text-white text-3xl mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">👥</div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Gestión de Clientes</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                                Mantén un historial completo de cada cliente, sus preferencias, visitas y comunicaciones para cerrar más ventas.
                            </p>
                            <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-full bg-gradient-to-r from-primary-500 to-accent-500 h-1 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border border-slate-200/50 dark:border-slate-600/50 hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 hover:border-primary-200 dark:hover:border-primary-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center text-white text-3xl mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">📊</div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Reportes y Analytics</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                                Analiza el rendimiento de tu negocio con reportes detallados sobre ventas, clientes y propiedades más populares.
                            </p>
                            <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-full bg-gradient-to-r from-primary-500 to-accent-500 h-1 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Feature 4 -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border border-slate-200/50 dark:border-slate-600/50 hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 hover:border-primary-200 dark:hover:border-primary-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center text-white text-3xl mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">📱</div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Acceso Móvil</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                                Gestiona tu negocio desde cualquier lugar con nuestra aplicación móvil optimizada para agentes inmobiliarios.
                            </p>
                            <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-full bg-gradient-to-r from-primary-500 to-accent-500 h-1 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Feature 5 -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border border-slate-200/50 dark:border-slate-600/50 hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 hover:border-primary-200 dark:hover:border-primary-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center text-white text-3xl mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">🔔</div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Notificaciones Inteligentes</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                                Recibe alertas automáticas sobre nuevas oportunidades, recordatorios de seguimiento y actualizaciones importantes.
                            </p>
                            <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-full bg-gradient-to-r from-primary-500 to-accent-500 h-1 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Feature 6 -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border border-slate-200/50 dark:border-slate-600/50 hover:-translate-y-3 hover:shadow-2xl transition-all duration-500 hover:border-primary-200 dark:hover:border-primary-700">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center text-white text-3xl mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">💼</div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Gestión de Tareas</h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                                Organiza tu agenda con tareas, recordatorios y seguimientos para nunca perder una oportunidad de venta.
                            </p>
                            <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-full bg-gradient-to-r from-primary-500 to-accent-500 h-1 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="relative py-24 bg-gradient-to-br from-primary-600 via-primary-700 to-accent-600 text-white overflow-hidden">
                <!-- Background Elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-primary-600/90 to-accent-600/90"></div>
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 60px 60px;"></div>
                    </div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl md:text-5xl font-bold mb-4">Números que Hablan por Sí Solos</h2>
                        <p class="text-xl opacity-90 max-w-2xl mx-auto">Miles de agentes inmobiliarios confían en Open9 CRM para hacer crecer su negocio</p>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        <div class="group space-y-4 p-6 rounded-2xl bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-5xl md:text-6xl font-bold animate-pulse-slow">500+</h3>
                            <p class="text-lg opacity-90 font-medium">Agentes Inmobiliarios</p>
                        </div>
                        
                        <div class="group space-y-4 p-6 rounded-2xl bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-5xl md:text-6xl font-bold animate-pulse-slow">10,000+</h3>
                            <p class="text-lg opacity-90 font-medium">Propiedades Gestionadas</p>
                        </div>
                        
                        <div class="group space-y-4 p-6 rounded-2xl bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-5xl md:text-6xl font-bold animate-pulse-slow">98%</h3>
                            <p class="text-lg opacity-90 font-medium">Satisfacción del Cliente</p>
                        </div>
                        
                        <div class="group space-y-4 p-6 rounded-2xl bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-5xl md:text-6xl font-bold animate-pulse-slow">24/7</h3>
                            <p class="text-lg opacity-90 font-medium">Soporte Técnico</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section id="demo" class="relative py-24 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-800 dark:via-slate-700 dark:to-slate-600 overflow-hidden">
                <!-- Background Elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-bl from-primary-200/20 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-accent-200/20 to-transparent rounded-full blur-3xl"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-accent-100 to-primary-100 dark:from-accent-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-medium mb-6 animate-bounce-slow">
                        🎯 ¡Actúa Ahora!
                    </div>
                    
                    <h2 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6">
                        ¿Listo para <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-500">Transformar</span> tu Negocio?
                    </h2>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-300 mb-10 max-w-3xl mx-auto leading-relaxed">
                        Únete a <span class="font-semibold text-primary-600 dark:text-primary-400">cientos de agentes inmobiliarios</span> que ya están usando Open9 CRM para vender más propiedades y hacer crecer sus ingresos de manera exponencial.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12">
                        <a href="#contact" class="group inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-primary-600 to-accent-500 text-white font-bold rounded-2xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-lg shadow-lg">
                            <span>Contactar Ventas</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </a>
                        <a href="#features" class="group inline-flex items-center gap-3 px-10 py-5 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-bold rounded-2xl hover:bg-primary-600 hover:text-white transition-all duration-300 text-lg hover:scale-105">
                            <span>Más Información</span>
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Social Proof -->
                    <div class="bg-white/50 dark:bg-slate-700/50 backdrop-blur-sm rounded-3xl p-8 border border-slate-200/50 dark:border-slate-600/50">
                        <p class="text-slate-600 dark:text-slate-300 mb-4 font-medium">💬 Lo que dicen nuestros clientes:</p>
                        <div class="flex flex-wrap justify-center items-center gap-6 text-sm text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-400">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.92 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                                <span>"Increíble plataforma"</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-400">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                                <span>"Facilita mi trabajo"</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-400">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                                <span>"Excelente soporte"</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section id="pricing" class="relative py-24 bg-white dark:bg-slate-800 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #3b82f6 1px, transparent 0); background-size: 50px 50px;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-20">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-accent-100 to-primary-100 dark:from-accent-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-medium mb-6">
                            💰 Planes y Precios
                        </div>
                        <h2 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6">
                            Elige el Plan <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-500">Perfecto</span> para Ti
                        </h2>
                        <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto leading-relaxed">
                            Planes flexibles que se adaptan a tu negocio, desde agentes independientes hasta grandes empresas inmobiliarias
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Starter Plan -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border-2 border-slate-200/50 dark:border-slate-600/50 hover:border-primary-300 dark:hover:border-primary-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Starter</h3>
                                <p class="text-slate-600 dark:text-slate-400 mb-6">Perfecto para agentes independientes</p>
                                <div class="mb-6">
                                    <span class="text-4xl font-bold text-slate-900 dark:text-white">$29</span>
                                    <span class="text-slate-600 dark:text-slate-400">/mes</span>
                                </div>
                                <a href="#contact" class="inline-flex items-center justify-center w-full px-6 py-3 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-primary-600 hover:text-white transition-all duration-300">
                                    Comenzar Gratis
                                </a>
                            </div>
                            <ul class="space-y-4">
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Hasta 100 propiedades</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Gestión básica de clientes</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Reportes básicos</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Soporte por email</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Professional Plan -->
                        <div class="group bg-gradient-to-br from-primary-50 to-accent-50 dark:from-primary-900/20 dark:to-accent-900/20 p-8 rounded-3xl border-2 border-primary-300 dark:border-primary-600 hover:border-primary-400 dark:hover:border-primary-500 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 relative">
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                <span class="bg-gradient-to-r from-primary-600 to-accent-500 text-white px-4 py-2 rounded-full text-sm font-semibold">Más Popular</span>
                            </div>
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Professional</h3>
                                <p class="text-slate-600 dark:text-slate-400 mb-6">Ideal para equipos pequeños</p>
                                <div class="mb-6">
                                    <span class="text-4xl font-bold text-slate-900 dark:text-white">$79</span>
                                    <span class="text-slate-600 dark:text-slate-400">/mes</span>
                                </div>
                                <a href="#contact" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-500 text-white font-semibold rounded-xl hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                    Comenzar Ahora
                                </a>
                            </div>
                            <ul class="space-y-4">
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Propiedades ilimitadas</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Hasta 5 usuarios</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Reportes avanzados</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Integraciones API</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Soporte prioritario</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Enterprise Plan -->
                        <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-700 dark:to-slate-800 p-8 rounded-3xl border-2 border-slate-200/50 dark:border-slate-600/50 hover:border-accent-300 dark:hover:border-accent-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Enterprise</h3>
                                <p class="text-slate-600 dark:text-slate-400 mb-6">Para grandes empresas</p>
                                <div class="mb-6">
                                    <span class="text-4xl font-bold text-slate-900 dark:text-white">$199</span>
                                    <span class="text-slate-600 dark:text-slate-400">/mes</span>
                                </div>
                                <a href="#contact" class="inline-flex items-center justify-center w-full px-6 py-3 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-accent-500 hover:text-white transition-all duration-300">
                                    Contactar Ventas
                                </a>
                            </div>
                            <ul class="space-y-4">
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Todo del plan Professional</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Usuarios ilimitados</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Personalización completa</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Soporte 24/7 dedicado</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-slate-600 dark:text-slate-300">Implementación personalizada</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section id="contact" class="relative py-24 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-800 dark:via-slate-700 dark:to-slate-600 overflow-hidden">
                <!-- Background Elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 left-0 w-80 h-80 bg-gradient-to-br from-primary-200/20 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-accent-200/20 to-transparent rounded-full blur-3xl"></div>
                </div>
                
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-accent-100 to-primary-100 dark:from-accent-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-medium mb-6 animate-bounce-slow">
                        📞 ¡Hablemos!
                    </div>
                    
                    <h2 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6">
                        ¿Listo para <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-500">Comenzar</span>?
                    </h2>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-300 mb-12 max-w-2xl mx-auto leading-relaxed">
                        Nuestro equipo está listo para ayudarte a implementar Open9 CRM en tu negocio inmobiliario
                    </p>
                    
                    <div class="bg-white/50 dark:bg-slate-700/50 backdrop-blur-sm rounded-3xl p-8 border border-slate-200/50 dark:border-slate-600/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="text-left">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Información de Contacto</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white">Teléfono</p>
                                            <p class="text-slate-600 dark:text-slate-300">+1 (555) 123-4567</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white">Email</p>
                                            <p class="text-slate-600 dark:text-slate-300">ventas@open9crm.com</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white">Oficina</p>
                                            <p class="text-slate-600 dark:text-slate-300">Madrid, España</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-left">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Envíanos un Mensaje</h3>
                                <form class="space-y-4">
                                    <div>
                                        <input type="text" placeholder="Tu nombre" class="w-full px-4 py-3 bg-white/70 dark:bg-slate-600/70 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                    <div>
                                        <input type="email" placeholder="Tu email" class="w-full px-4 py-3 bg-white/70 dark:bg-slate-600/70 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                    <div>
                                        <textarea rows="4" placeholder="Tu mensaje" class="w-full px-4 py-3 bg-white/70 dark:bg-slate-600/70 border border-slate-200 dark:border-slate-500 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-500 text-white font-semibold rounded-xl hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                        Enviar Mensaje
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-slate-400 py-12 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #3b82f6 1px, transparent 0); background-size: 60px 60px;"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <!-- Company Info -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-accent-500 rounded-xl flex items-center justify-center text-white font-bold text-lg">9</div>
                            <span class="text-xl font-bold text-white">Open9 CRM</span>
                        </div>
                        <p class="text-slate-300 mb-4 max-w-md">
                            La plataforma líder en CRM inmobiliario que transforma la forma en que los agentes gestionan sus negocios y venden propiedades.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 bg-slate-700 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.107-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-slate-700 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-slate-700 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">Producto</h3>
                        <ul class="space-y-2">
                            <li><a href="#features" class="hover:text-primary-400 transition-colors duration-200">Características</a></li>
                            <li><a href="#pricing" class="hover:text-primary-400 transition-colors duration-200">Precios</a></li>
                            <li><a href="#demo" class="hover:text-primary-400 transition-colors duration-200">Demo</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors duration-200">API</a></li>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">Soporte</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-primary-400 transition-colors duration-200">Centro de Ayuda</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors duration-200">Documentación</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors duration-200">Contacto</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors duration-200">Estado del Sistema</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-slate-700 pt-8 text-center">
                    <p>&copy; 2024 Open9 CRM Inmobiliaria. Todos los derechos reservados.</p>
                    <div class="flex justify-center space-x-6 mt-4 text-sm">
                        <a href="#" class="hover:text-primary-400 transition-colors duration-200">Privacidad</a>
                        <a href="#" class="hover:text-primary-400 transition-colors duration-200">Términos</a>
                        <a href="#" class="hover:text-primary-400 transition-colors duration-200">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
