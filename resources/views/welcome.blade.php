<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Open9 CRM Inmobiliaria - La solución completa para gestionar tu negocio inmobiliario con inteligencia artificial y automatización">

        <title>Open9 CRM Inmobiliaria - Revoluciona tu Negocio Inmobiliario</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800,900" rel="stylesheet" />

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
                                50: '#f0f9ff',
                                100: '#e0f2fe',
                                200: '#bae6fd',
                                300: '#7dd3fc',
                                400: '#38bdf8',
                                500: '#0ea5e9',
                                600: '#0284c7',
                                700: '#0369a1',
                                800: '#075985',
                                900: '#0c4a6e',
                                950: '#082f49',
                            },
                            'accent': {
                                50: '#f0fdf4',
                                100: '#dcfce7',
                                200: '#bbf7d0',
                                300: '#86efac',
                                400: '#4ade80',
                                500: '#22c55e',
                                600: '#16a34a',
                                700: '#15803d',
                                800: '#166534',
                                900: '#14532d',
                                950: '#052e16',
                            },
                            'success': {
                                50: '#f0fdf4',
                                100: '#dcfce7',
                                200: '#bbf7d0',
                                300: '#86efac',
                                400: '#4ade80',
                                500: '#22c55e',
                                600: '#16a34a',
                                700: '#15803d',
                                800: '#166534',
                                900: '#14532d',
                            },
                            'warning': {
                                50: '#fffbeb',
                                100: '#fef3c7',
                                200: '#fde68a',
                                300: '#fcd34d',
                                400: '#fbbf24',
                                500: '#f59e0b',
                                600: '#d97706',
                                700: '#b45309',
                                800: '#92400e',
                                900: '#78350f',
                            },
                            'error': {
                                50: '#fef2f2',
                                100: '#fee2e2',
                                200: '#fecaca',
                                300: '#fca5a5',
                                400: '#f87171',
                                500: '#ef4444',
                                600: '#dc2626',
                                700: '#b91c1c',
                                800: '#991b1b',
                                900: '#7f1d1d',
                            }
                        },
                        animation: {
                            'float': 'float 6s ease-in-out infinite',
                            'float-reverse': 'float-reverse 8s ease-in-out infinite',
                            'bounce-slow': 'bounce 3s infinite',
                            'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                            'fade-in': 'fade-in 0.5s ease-out',
                            'slide-up': 'slide-up 0.6s ease-out',
                            'slide-right': 'slide-right 0.6s ease-out',
                            'scale-in': 'scale-in 0.4s ease-out',
                            'wiggle': 'wiggle 1s ease-in-out infinite',
                            'gradient': 'gradient 15s ease infinite',
                            'shimmer': 'shimmer 2s linear infinite',
                        },
                        keyframes: {
                            float: {
                                '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                                '50%': { transform: 'translateY(-20px) rotate(5deg)' },
                            },
                            'float-reverse': {
                                '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                                '50%': { transform: 'translateY(15px) rotate(-3deg)' },
                            },
                            'fade-in': {
                                '0%': { opacity: '0' },
                                '100%': { opacity: '1' },
                            },
                            'slide-up': {
                                '0%': { transform: 'translateY(30px)', opacity: '0' },
                                '100%': { transform: 'translateY(0)', opacity: '1' },
                            },
                            'slide-right': {
                                '0%': { transform: 'translateX(-30px)', opacity: '0' },
                                '100%': { transform: 'translateX(0)', opacity: '1' },
                            },
                            'scale-in': {
                                '0%': { transform: 'scale(0.9)', opacity: '0' },
                                '100%': { transform: 'scale(1)', opacity: '1' },
                            },
                            wiggle: {
                                '0%, 100%': { transform: 'rotate(-3deg)' },
                                '50%': { transform: 'rotate(3deg)' },
                            },
                            gradient: {
                                '0%, 100%': { 'background-position': '0% 50%' },
                                '50%': { 'background-position': '100% 50%' },
                            },
                            shimmer: {
                                '0%': { transform: 'translateX(-100%)' },
                                '100%': { transform: 'translateX(100%)' },
                            }
                        },
                        backgroundImage: {
                            'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                            'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="font-poppins bg-gradient-to-br from-blue-50 via-cyan-50 to-emerald-50 dark:from-slate-900 dark:via-blue-900 dark:to-emerald-900 text-slate-800 dark:text-slate-200 min-h-screen overflow-x-hidden">
        <!-- Header Mejorado -->
        <header class="sticky top-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-b border-blue-200/50 dark:border-blue-700/50 shadow-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex justify-between items-center py-4">
                    <!-- Logo Open9 -->
                    <a href="#" class="group flex items-center gap-3 hover:scale-105 transition-all duration-300">
                        <img src="{{ asset('images/Open9/logo_completo_sin_fondo.png') }}" alt="Open9" class="h-12 object-contain" />
                    </a>

                    <!-- Navegación Desktop -->
                    <div class="hidden lg:flex items-center gap-8">
                        <a href="#features" class="relative text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-all duration-300 hover:scale-105 group">
                            <span class="relative z-10">Características</span>
                            <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-primary-500 via-cyan-500 to-accent-500 group-hover:w-full transition-all duration-300"></div>
                        </a>
                        <a href="#pricing" class="relative text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-all duration-300 hover:scale-105 group">
                            <span class="relative z-10">Precios</span>
                            <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-primary-500 via-cyan-500 to-accent-500 group-hover:w-full transition-all duration-300"></div>
                        </a>
                        <a href="#testimonials" class="relative text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-all duration-300 hover:scale-105 group">
                            <span class="relative z-10">Testimonios</span>
                            <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-primary-500 via-cyan-500 to-accent-500 group-hover:w-full transition-all duration-300"></div>
                        </a>
                        <a href="#contact" class="relative text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-all duration-300 hover:scale-105 group">
                            <span class="relative z-10">Contacto</span>
                            <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-primary-500 via-cyan-500 to-accent-500 group-hover:w-full transition-all duration-300"></div>
                        </a>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="group inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white font-semibold rounded-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 shadow-lg relative overflow-hidden">
                                    <span class="relative z-10">Dashboard</span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-accent-500 via-cyan-500 to-primary-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-2 px-6 py-3 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-semibold rounded-xl hover:bg-primary-600 hover:text-white transition-all duration-300 hover:scale-105 group">
                                    <span>Iniciar Sesión</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                </a>
                            @endauth
                        @endif
                        @if (Route::has('register'))
                            <div class="hidden lg:flex items-center gap-2">
                                <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white font-semibold rounded-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 shadow-lg relative overflow-hidden">
                                    <span class="relative z-10">Cazador</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    <div class="absolute inset-0 bg-gradient-to-r from-accent-500 via-cyan-500 to-primary-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </a>
                                <a href="{{ route('register-datero') }}" class="group inline-flex items-center gap-2 px-4 py-3 border-2 border-accent-500 text-accent-600 dark:text-accent-400 font-semibold rounded-xl hover:bg-accent-500 hover:text-white transition-all duration-300 hover:scale-105">
                                    <span>Datero</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif

                        <!-- Botón Mobile Menu -->
                        <button class="lg:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-200" onclick="toggleMobileMenu()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </nav>

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden lg:hidden py-4 border-t border-blue-200 dark:border-blue-700">
                    <div class="flex flex-col space-y-4">
                        <a href="#features" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200 py-2">Características</a>
                        <a href="#pricing" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200 py-2">Precios</a>
                        <a href="#testimonials" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200 py-2">Testimonios</a>
                        <a href="#contact" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors duration-200 py-2">Contacto</a>
                        @if (Route::has('login') && !auth()->check())
                            <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 font-semibold py-2">Iniciar Sesión</a>
                        @endif
                        @if (Route::has('register') && !auth()->check())
                            <div class="pt-2 border-t border-blue-200 dark:border-blue-700">
                                <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white font-semibold rounded-xl mb-2">Registro</a>
                                <a href="{{ route('register-datero') }}" class="block w-full text-center px-4 py-3 border-2 border-accent-500 text-accent-600 dark:text-accent-400 font-semibold rounded-xl">Registro Datero</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main>
            <!-- Hero Section Mejorada -->
            <section class="relative min-h-screen flex items-center justify-center px-4 overflow-hidden">
                <!-- Background Elements Mejorados -->
                <div class="absolute inset-0 overflow-hidden">
                    <!-- Gradientes animados -->
                    <div class="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-primary-200/40 via-cyan-200/40 to-accent-200/40 rounded-full blur-3xl animate-float"></div>
                    <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-gradient-to-tr from-blue-200/30 via-cyan-200/30 to-emerald-200/30 rounded-full blur-3xl animate-float-reverse" style="animation-delay: -2s;"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-primary-200/20 via-cyan-200/20 to-accent-200/20 rounded-full blur-3xl animate-pulse-slow"></div>
                    
                    <!-- Patrones decorativos -->
                    <div class="absolute inset-0 opacity-5">
                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #0ea5e9 1px, transparent 0); background-size: 50px 50px;"></div>
                    </div>
                </div>
                
                <div class="max-w-7xl mx-auto text-center relative z-10">
                    <!-- Badge de confianza mejorado -->
                    <div class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-100 via-cyan-100 to-accent-100 dark:from-primary-900/30 dark:via-cyan-900/30 dark:to-accent-900/30 rounded-full text-primary-700 dark:text-primary-300 font-semibold mb-8 animate-bounce-slow border border-primary-200/50 dark:border-primary-700/50 shadow-lg">
                        <div class="w-2 h-2 bg-accent-500 rounded-full animate-pulse"></div>
                        <span>🚀 La plataforma líder en CRM Inmobiliario</span>
                        <div class="w-2 h-2 bg-accent-500 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                    </div>
                    
                    <!-- Título principal mejorado -->
                    <h1 class="text-6xl md:text-8xl lg:text-9xl font-black mb-8 leading-tight">
                        <span class="block bg-gradient-to-r from-slate-900 via-primary-700 to-cyan-600 dark:from-slate-100 dark:via-primary-300 dark:to-cyan-400 bg-clip-text text-transparent animate-fade-in">
                            Revoluciona tu
                        </span>
                        <span class="block bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 bg-clip-text text-transparent animate-slide-up" style="animation-delay: 0.2s;">
                            Negocio Inmobiliario
                        </span>
                        <span class="block text-4xl md:text-6xl lg:text-7xl font-bold text-slate-600 dark:text-slate-300 animate-slide-up" style="animation-delay: 0.4s;">
                            con Open9 CRM
                        </span>
                    </h1>
                    
                    <!-- Subtítulo mejorado -->
                    <p class="text-xl md:text-2xl lg:text-3xl text-slate-600 dark:text-slate-300 mb-12 max-w-5xl mx-auto leading-relaxed animate-slide-up" style="animation-delay: 0.6s;">
                        La plataforma más avanzada para agentes inmobiliarios que quieren 
                        <span class="font-bold text-transparent bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 bg-clip-text">
                            vender más propiedades
                        </span>, 
                        <span class="font-bold text-transparent bg-gradient-to-r from-accent-500 via-cyan-500 to-primary-600 bg-clip-text">
                            gestionar clientes eficientemente
                        </span> 
                        y hacer crecer su negocio de manera exponencial.
                    </p>
                    
                    <!-- Botones de acción mejorados -->
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16 animate-scale-in" style="animation-delay: 0.8s;">
                        <a href="#demo" class="group relative inline-flex items-center gap-3 px-12 py-6 bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white font-bold rounded-2xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-xl shadow-xl overflow-hidden">
                            <span class="relative z-10">Solicitar Demo Gratis</span>
                            <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            <div class="absolute inset-0 bg-gradient-to-r from-accent-500 via-cyan-500 to-primary-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        <a href="#features" class="group inline-flex items-center gap-3 px-12 py-6 border-2 border-primary-600 text-primary-600 dark:text-primary-400 font-bold rounded-2xl hover:bg-primary-600 hover:text-white transition-all duration-300 text-xl hover:scale-105 hover:shadow-xl">
                            <span>Ver Características</span>
                            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <!-- Indicadores de confianza mejorados -->
                    <div class="flex flex-wrap justify-center items-center gap-8 text-slate-500 dark:text-slate-400 animate-fade-in" style="animation-delay: 1s;">
                        <div class="flex items-center gap-3 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm px-4 py-2 rounded-full border border-blue-200/50 dark:border-blue-700/50">
                            <div class="w-6 h-6 bg-accent-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="font-semibold">Sin costos ocultos</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm px-4 py-2 rounded-full border border-blue-200/50 dark:border-blue-700/50">
                            <div class="w-6 h-6 bg-accent-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="font-semibold">Implementación en 24h</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm px-4 py-2 rounded-full border border-blue-200/50 dark:border-blue-700/50">
                            <div class="w-6 h-6 bg-accent-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="font-semibold">Soporte 24/7</span>
                        </div>
                    </div>
                </div>

                <!-- Scroll indicator -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                    <div class="w-6 h-10 border-2 border-primary-400 dark:border-primary-500 rounded-full flex justify-center">
                        <div class="w-1 h-3 bg-primary-400 dark:bg-primary-500 rounded-full mt-2 animate-pulse"></div>
                    </div>
                </div>
            </section>

            <!-- Features Section Mejorada -->
            <section id="features" class="relative py-32 bg-gradient-to-br from-white via-blue-50 to-cyan-50 dark:from-slate-900 dark:via-blue-900 dark:to-cyan-900 overflow-hidden">
                <!-- Background Elements Mejorados -->
                <div class="absolute inset-0">
                    <div class="absolute inset-0 opacity-5">
                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #0ea5e9 1px, transparent 0); background-size: 60px 60px;"></div>
                    </div>
                    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-bl from-primary-200/20 via-cyan-200/20 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-accent-200/20 via-emerald-200/20 to-transparent rounded-full blur-3xl"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <!-- Header de la sección -->
                    <div class="text-center mb-20">
                        <div class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-accent-100 via-cyan-100 to-primary-100 dark:from-accent-900/30 dark:via-cyan-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-semibold mb-8 border border-accent-200/50 dark:border-accent-700/50 shadow-lg">
                            <span class="text-2xl">✨</span>
                            <span>Características Principales</span>
                        </div>
                        <h2 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-white mb-8 leading-tight">
                            Todo lo que necesitas en un 
                            <span class="text-transparent bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 bg-clip-text animate-gradient">
                                CRM Inmobiliario
                            </span>
                        </h2>
                        <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-300 max-w-4xl mx-auto leading-relaxed">
                            Herramientas poderosas diseñadas específicamente para el sector inmobiliario que 
                            <span class="font-bold text-primary-600 dark:text-primary-400">transformarán</span> 
                            tu forma de trabajar
                        </p>
                    </div>
                    
                    <!-- Grid de características mejorado -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1 - Gestión de Propiedades -->
                        <div class="group relative bg-gradient-to-br from-white to-blue-50 dark:from-slate-800 dark:to-blue-900 p-8 rounded-3xl border border-blue-200/50 dark:border-blue-600/50 hover:-translate-y-4 hover:shadow-2xl transition-all duration-500 hover:border-primary-300 dark:hover:border-primary-600 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 via-cyan-500/5 to-accent-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-primary-500 via-cyan-500 to-accent-500 rounded-3xl flex items-center justify-center text-white text-4xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl">
                                    🏠
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    Gestión de Propiedades
                                </h3>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg mb-6">
                                    Administra tu inventario de propiedades con fotos, descripciones, precios y estado de disponibilidad en tiempo real.
                                </p>
                                <div class="flex items-center text-primary-600 dark:text-primary-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Saber más</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 via-cyan-500 to-accent-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>

                        <!-- Feature 2 - Gestión de Clientes -->
                        <div class="group relative bg-gradient-to-br from-white to-cyan-50 dark:from-slate-800 dark:to-cyan-900 p-8 rounded-3xl border border-cyan-200/50 dark:border-cyan-600/50 hover:-translate-y-4 hover:shadow-2xl transition-all duration-500 hover:border-primary-300 dark:hover:border-primary-600 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 via-cyan-500/5 to-accent-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-accent-500 via-cyan-500 to-primary-500 rounded-3xl flex items-center justify-center text-white text-4xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl">
                                    👥
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    Gestión de Clientes
                                </h3>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg mb-6">
                                    Mantén un historial completo de cada cliente, sus preferencias, visitas y comunicaciones para cerrar más ventas.
                                </p>
                                <div class="flex items-center text-primary-600 dark:text-primary-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Saber más</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-accent-500 via-cyan-500 to-primary-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>

                        <!-- Feature 3 - Reportes y Analytics -->
                        <div class="group relative bg-gradient-to-br from-white to-emerald-50 dark:from-slate-800 dark:to-emerald-900 p-8 rounded-3xl border border-emerald-200/50 dark:border-emerald-600/50 hover:-translate-y-4 hover:shadow-2xl transition-all duration-500 hover:border-primary-300 dark:hover:border-primary-600 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 via-cyan-500/5 to-accent-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-primary-500 via-cyan-500 to-accent-500 rounded-3xl flex items-center justify-center text-white text-4xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl">
                                    📊
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    Reportes y Analytics
                                </h3>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg mb-6">
                                    Analiza el rendimiento de tu negocio con reportes detallados sobre ventas, clientes y propiedades más populares.
                                </p>
                                <div class="flex items-center text-primary-600 dark:text-primary-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Saber más</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 via-cyan-500 to-accent-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>

                        <!-- Feature 4 - Acceso Móvil -->
                        <div class="group relative bg-gradient-to-br from-white to-blue-50 dark:from-slate-800 dark:to-blue-900 p-8 rounded-3xl border border-blue-200/50 dark:border-blue-600/50 hover:-translate-y-4 hover:shadow-2xl transition-all duration-500 hover:border-primary-300 dark:hover:border-primary-600 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 via-cyan-500/5 to-accent-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-accent-500 via-cyan-500 to-primary-500 rounded-3xl flex items-center justify-center text-white text-4xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl">
                                    📱
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    Acceso Móvil
                                </h3>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg mb-6">
                                    Gestiona tu negocio desde cualquier lugar con nuestra aplicación móvil optimizada para agentes inmobiliarios.
                                </p>
                                <div class="flex items-center text-primary-600 dark:text-primary-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Saber más</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-accent-500 via-cyan-500 to-primary-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>

                        <!-- Feature 5 - Notificaciones Inteligentes -->
                        <div class="group relative bg-gradient-to-br from-white to-cyan-50 dark:from-slate-800 dark:to-cyan-900 p-8 rounded-3xl border border-cyan-200/50 dark:border-cyan-600/50 hover:-translate-y-4 hover:shadow-2xl transition-all duration-500 hover:border-primary-300 dark:hover:border-primary-600 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 via-cyan-500/5 to-accent-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-primary-500 via-cyan-500 to-accent-500 rounded-3xl flex items-center justify-center text-white text-4xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl">
                                    🔔
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    Notificaciones Inteligentes
                                </h3>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg mb-6">
                                    Recibe alertas automáticas sobre nuevas oportunidades, recordatorios de seguimiento y actualizaciones importantes.
                                </p>
                                <div class="flex items-center text-primary-600 dark:text-primary-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Saber más</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 via-cyan-500 to-accent-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>

                        <!-- Feature 6 - Gestión de Tareas -->
                        <div class="group relative bg-gradient-to-br from-white to-emerald-50 dark:from-slate-800 dark:to-emerald-900 p-8 rounded-3xl border border-emerald-200/50 dark:border-emerald-600/50 hover:-translate-y-4 hover:shadow-2xl transition-all duration-500 hover:border-primary-300 dark:hover:border-primary-600 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 via-cyan-500/5 to-accent-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-accent-500 via-cyan-500 to-primary-500 rounded-3xl flex items-center justify-center text-white text-4xl mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl">
                                    💼
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    Gestión de Tareas
                                </h3>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg mb-6">
                                    Organiza tu agenda con tareas, recordatorios y seguimientos para nunca perder una oportunidad de venta.
                                </p>
                                <div class="flex items-center text-primary-600 dark:text-primary-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Saber más</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-accent-500 via-cyan-500 to-primary-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="relative py-24 bg-gradient-to-br from-primary-600 via-cyan-600 to-accent-600 text-white overflow-hidden">
                <!-- Background Elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-primary-600/90 via-cyan-600/90 to-accent-600/90"></div>
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
            <section id="demo" class="relative py-24 bg-gradient-to-br from-blue-50 via-cyan-50 to-emerald-50 dark:from-slate-800 dark:via-blue-800 dark:to-emerald-800 overflow-hidden">
                <!-- Background Elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-bl from-primary-200/20 via-cyan-200/20 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-accent-200/20 via-emerald-200/20 to-transparent rounded-full blur-3xl"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-accent-100 via-cyan-100 to-primary-100 dark:from-accent-900/30 dark:via-cyan-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-medium mb-6 animate-bounce-slow">
                        🎯 ¡Actúa Ahora!
                    </div>
                    
                    <h2 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6">
                        ¿Listo para <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500">Transformar</span> tu Negocio?
                    </h2>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-300 mb-10 max-w-3xl mx-auto leading-relaxed">
                        Únete a <span class="font-semibold text-primary-600 dark:text-primary-400">cientos de agentes inmobiliarios</span> que ya están usando Open9 CRM para vender más propiedades y hacer crecer sus ingresos de manera exponencial.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12">
                        @if (Route::has('register') && !auth()->check())
                            <a href="{{ route('register') }}" class="group inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white font-bold rounded-2xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-lg shadow-lg">
                                <span>Comenzar Gratis</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                            <a href="{{ route('register-datero') }}" class="group inline-flex items-center gap-3 px-10 py-5 border-2 border-accent-500 text-accent-600 dark:text-accent-400 font-bold rounded-2xl hover:bg-accent-500 hover:text-white transition-all duration-300 text-lg hover:scale-105">
                                <span>Registro Datero</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        @else
                            <a href="#contact" class="group inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white font-bold rounded-2xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-lg shadow-lg">
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
                        @endif
                    </div>
                    
                    <!-- Social Proof -->
                    <div class="bg-white/50 dark:bg-slate-700/50 backdrop-blur-sm rounded-3xl p-8 border border-blue-200/50 dark:border-blue-600/50">
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
            <section id="pricing" class="relative py-24 bg-gradient-to-br from-white via-blue-50 to-cyan-50 dark:from-slate-800 dark:via-blue-900 dark:to-cyan-900 overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #0ea5e9 1px, transparent 0); background-size: 50px 50px;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-20">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-accent-100 via-cyan-100 to-primary-100 dark:from-accent-900/30 dark:via-cyan-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-medium mb-6">
                            💰 Planes y Precios
                        </div>
                        <h2 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6">
                            Elige el Plan <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500">Perfecto</span> para Ti
                        </h2>
                        <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto leading-relaxed">
                            Planes flexibles que se adaptan a tu negocio, desde agentes independientes hasta grandes empresas inmobiliarias
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Starter Plan -->
                        <div class="group bg-gradient-to-br from-white to-blue-50 dark:from-slate-700 dark:to-blue-800 p-8 rounded-3xl border-2 border-blue-200/50 dark:border-blue-600/50 hover:border-primary-300 dark:hover:border-primary-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Starter</h3>
                                <p class="text-slate-600 dark:text-slate-400 mb-6">Perfecto para agentes independientes</p>
                                <div class="mb-6">
                                    <span class="text-4xl font-bold text-slate-900 dark:text-white">$29</span>
                                    <span class="text-slate-600 dark:text-slate-400">/mes</span>
                                </div>
                                @if (Route::has('register') && !auth()->check())
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-primary-600 hover:text-white transition-all duration-300">
                                        Comenzar Gratis
                                    </a>
                                @else
                                    <a href="#contact" class="inline-flex items-center justify-center w-full px-6 py-3 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-primary-600 hover:text-white transition-all duration-300">
                                        Contactar Ventas
                                    </a>
                                @endif
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
                        <div class="group bg-gradient-to-br from-primary-50 via-cyan-50 to-accent-50 dark:from-primary-900/20 dark:via-cyan-900/20 dark:to-accent-900/20 p-8 rounded-3xl border-2 border-primary-300 dark:border-primary-600 hover:border-primary-400 dark:hover:border-primary-500 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 relative">
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                <span class="bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white px-4 py-2 rounded-full text-sm font-semibold">Más Popular</span>
                            </div>
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Professional</h3>
                                <p class="text-slate-600 dark:text-slate-400 mb-6">Ideal para equipos pequeños</p>
                                <div class="mb-6">
                                    <span class="text-4xl font-bold text-slate-900 dark:text-white">$79</span>
                                    <span class="text-slate-600 dark:text-slate-400">/mes</span>
                                </div>
                                @if (Route::has('register') && !auth()->check())
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-500 text-white font-semibold rounded-xl hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                        Comenzar Ahora
                                    </a>
                                @else
                                    <a href="#contact" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-500 text-white font-semibold rounded-xl hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                        Contactar Ventas
                                    </a>
                                @endif
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
                        <div class="group bg-gradient-to-br from-white to-emerald-50 dark:from-slate-700 dark:to-emerald-800 p-8 rounded-3xl border-2 border-emerald-200/50 dark:border-emerald-600/50 hover:border-accent-300 dark:hover:border-accent-600 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500">
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
            <section id="contact" class="relative py-24 bg-gradient-to-br from-blue-50 via-cyan-50 to-emerald-50 dark:from-slate-800 dark:via-blue-800 dark:to-emerald-800 overflow-hidden">
                <!-- Background Elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 left-0 w-80 h-80 bg-gradient-to-br from-primary-200/20 via-cyan-200/20 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-accent-200/20 via-emerald-200/20 to-transparent rounded-full blur-3xl"></div>
                </div>
                
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-accent-100 via-cyan-100 to-primary-100 dark:from-accent-900/30 dark:via-cyan-900/30 dark:to-primary-900/30 rounded-full text-accent-700 dark:text-accent-300 font-medium mb-6 animate-bounce-slow">
                        📞 ¡Hablemos!
                    </div>
                    
                    <h2 class="text-4xl md:text-6xl font-bold text-slate-900 dark:text-white mb-6">
                        ¿Listo para <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500">Comenzar</span>?
                    </h2>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-300 mb-12 max-w-2xl mx-auto leading-relaxed">
                        Nuestro equipo está listo para ayudarte a implementar Open9 CRM en tu negocio inmobiliario
                    </p>
                    
                    <div class="bg-white/50 dark:bg-slate-700/50 backdrop-blur-sm rounded-3xl p-8 border border-blue-200/50 dark:border-blue-600/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="text-left">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Información de Contacto</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 via-cyan-500 to-accent-500 rounded-lg flex items-center justify-center">
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
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 via-cyan-500 to-accent-500 rounded-lg flex items-center justify-center">
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
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 via-cyan-500 to-accent-500 rounded-lg flex items-center justify-center">
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
                                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-primary-600 via-cyan-500 to-accent-500 text-white font-semibold rounded-xl hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
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
        <footer class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-emerald-900 text-slate-400 py-12 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #0ea5e9 1px, transparent 0); background-size: 60px 60px;"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <!-- Company Info -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-600 via-cyan-500 to-accent-500 rounded-xl flex items-center justify-center text-white font-bold text-lg">9</div>
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

        <!-- JavaScript para funcionalidades interactivas -->
        <script>
            // Función para toggle del menú móvil
            function toggleMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                mobileMenu.classList.toggle('hidden');
            }

            // Smooth scrolling para enlaces de navegación
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Animaciones al hacer scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            // Observar elementos para animaciones
            document.querySelectorAll('.group').forEach(el => {
                observer.observe(el);
            });

            // Efecto parallax suave para elementos de fondo
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const parallaxElements = document.querySelectorAll('.animate-float, .animate-float-reverse');
                
                parallaxElements.forEach((element, index) => {
                    const speed = 0.5 + (index * 0.1);
                    element.style.transform = `translateY(${scrolled * speed}px)`;
                });
            });

            // Efecto de typing para el título principal
            function typeWriter(element, text, speed = 100) {
                let i = 0;
                element.innerHTML = '';
                
                function type() {
                    if (i < text.length) {
                        element.innerHTML += text.charAt(i);
                        i++;
                        setTimeout(type, speed);
                    }
                }
                type();
            }

            // Inicializar efectos cuando la página esté cargada
            document.addEventListener('DOMContentLoaded', function() {
                // Agregar clase de animación a elementos
                const animatedElements = document.querySelectorAll('h1, h2, .group');
                animatedElements.forEach((el, index) => {
                    el.style.animationDelay = `${index * 0.1}s`;
                });
            });

            // Efecto de hover mejorado para botones
            document.querySelectorAll('a[class*="group"]').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        </script>
    </body>
</html>
