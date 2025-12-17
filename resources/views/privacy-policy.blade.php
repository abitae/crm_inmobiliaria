<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Política de Privacidad - CRM Inmobiliaria. Información sobre cómo recopilamos, usamos y protegemos tus datos personales.">
    
    <title>Política de Privacidad - CRM Inmobiliaria</title>
    
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
                },
            },
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('welcome') }}" class="text-primary-600 hover:text-primary-700 font-semibold">
                    ← Volver al inicio
                </a>
                <h1 class="text-xl font-bold text-gray-900">Política de Privacidad</h1>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8 lg:p-10">
            
            <!-- Introduction -->
            <div class="mb-8">
                <p class="text-gray-600 mb-4">
                    <strong>Última actualización:</strong> {{ date('d/m/Y') }}
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Esta Política de Privacidad describe cómo recopilamos, usamos, almacenamos y protegemos 
                    la información personal que nos proporcionas cuando utilizas nuestra aplicación móvil 
                    CRM Inmobiliaria. Al utilizar nuestra aplicación, aceptas las prácticas descritas en 
                    esta política.
                </p>
            </div>

            <!-- Section 1 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Información que Recopilamos</h2>
                
                <h3 class="text-xl font-semibold text-gray-800 mb-3 mt-6">1.1 Información Personal</h3>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Recopilamos la siguiente información personal cuando te registras y utilizas nuestra aplicación:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Datos de identificación:</strong> Nombre completo, número de documento de identidad (DNI), fecha de nacimiento</li>
                    <li><strong>Información de contacto:</strong> Dirección de correo electrónico, número de teléfono, dirección física</li>
                    <li><strong>Información profesional:</strong> Ocupación, datos bancarios (banco, cuenta bancaria, CCI) para el pago de comisiones</li>
                    <li><strong>Credenciales de acceso:</strong> PIN de 6 dígitos para autenticación</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-800 mb-3 mt-6">1.2 Información del Dispositivo</h3>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Cuando utilizas nuestra aplicación móvil, podemos recopilar automáticamente:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Dirección IP y ubicación aproximada</li>
                    <li>Tipo de dispositivo y sistema operativo</li>
                    <li>Identificadores únicos del dispositivo</li>
                    <li>Información de uso de la aplicación (logs de actividad)</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-800 mb-3 mt-6">1.3 Información de Clientes</h3>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Como parte de la funcionalidad del CRM, también recopilamos información sobre los clientes 
                    que registras en el sistema, incluyendo:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Datos personales de clientes (nombre, DNI, teléfono, dirección)</li>
                    <li>Información comercial (tipo de cliente, fuente, estado, score)</li>
                    <li>Historial de interacciones y actividades</li>
                    <li>Documentos y archivos relacionados</li>
                </ul>
            </section>

            <!-- Section 2 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Cómo Utilizamos tu Información</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Utilizamos la información recopilada para los siguientes propósitos:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Provisión del servicio:</strong> Para permitirte acceder y utilizar todas las funcionalidades del CRM</li>
                    <li><strong>Gestión de clientes:</strong> Para almacenar, organizar y gestionar la información de tus clientes</li>
                    <li><strong>Comunicación:</strong> Para contactarte sobre actualizaciones, cambios en el servicio o información importante</li>
                    <li><strong>Pago de comisiones:</strong> Para procesar y realizar pagos de comisiones utilizando tus datos bancarios</li>
                    <li><strong>Seguridad:</strong> Para autenticar tu identidad y proteger tu cuenta contra accesos no autorizados</li>
                    <li><strong>Mejora del servicio:</strong> Para analizar el uso de la aplicación y mejorar nuestras funcionalidades</li>
                    <li><strong>Cumplimiento legal:</strong> Para cumplir con obligaciones legales y regulatorias</li>
                </ul>
            </section>

            <!-- Section 3 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Compartir Información</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    No vendemos ni alquilamos tu información personal. Podemos compartir tu información únicamente en las siguientes circunstancias:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Con tu consentimiento:</strong> Cuando nos autorizas explícitamente a compartir información</li>
                    <li><strong>Proveedores de servicios:</strong> Con terceros que nos ayudan a operar la aplicación (hosting, análisis, etc.), bajo estrictos acuerdos de confidencialidad</li>
                    <li><strong>Requisitos legales:</strong> Cuando sea necesario para cumplir con leyes, regulaciones o procesos legales</li>
                    <li><strong>Protección de derechos:</strong> Para proteger nuestros derechos, propiedad o seguridad, o la de nuestros usuarios</li>
                    <li><strong>Transferencias empresariales:</strong> En caso de fusión, adquisición o venta de activos, con notificación previa</li>
                </ul>
            </section>

            <!-- Section 4 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Seguridad de los Datos</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Implementamos medidas de seguridad técnicas y organizativas para proteger tu información personal:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Cifrado:</strong> Utilizamos cifrado SSL/TLS para proteger la transmisión de datos</li>
                    <li><strong>Autenticación:</strong> Sistema de autenticación mediante JWT (JSON Web Tokens) y PIN de 6 dígitos</li>
                    <li><strong>Contraseñas hasheadas:</strong> Las contraseñas y PINs se almacenan utilizando algoritmos de hash seguros</li>
                    <li><strong>Control de acceso:</strong> Implementamos controles de acceso basados en roles para limitar quién puede ver o modificar información</li>
                    <li><strong>Monitoreo:</strong> Realizamos monitoreo continuo de seguridad y registramos actividades para detectar accesos no autorizados</li>
                    <li><strong>Backups seguros:</strong> Realizamos copias de seguridad regulares de los datos</li>
                </ul>
                <p class="text-gray-700 mt-4 leading-relaxed">
                    A pesar de nuestros esfuerzos, ningún método de transmisión por Internet o almacenamiento 
                    electrónico es 100% seguro. No podemos garantizar la seguridad absoluta de tus datos, 
                    pero nos comprometemos a notificarte en caso de una violación de seguridad que pueda 
                    afectar tu información personal.
                </p>
            </section>

            <!-- Section 5 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Retención de Datos</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Conservamos tu información personal durante el tiempo necesario para cumplir con los 
                    propósitos descritos en esta política, a menos que la ley requiera o permita un período 
                    de retención más largo. Específicamente:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Mantenemos los datos de tu cuenta mientras tu cuenta esté activa</li>
                    <li>Conservamos registros de actividad y logs según los requisitos legales aplicables</li>
                    <li>Los datos de clientes se mantienen mientras sean necesarios para la gestión comercial</li>
                    <li>Al cerrar tu cuenta, eliminaremos o anonimizaremos tu información personal, salvo 
                        cuando la ley requiera su conservación</li>
                </ul>
            </section>

            <!-- Section 6 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Tus Derechos</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Tienes los siguientes derechos respecto a tu información personal:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Acceso:</strong> Puedes solicitar una copia de la información personal que tenemos sobre ti</li>
                    <li><strong>Rectificación:</strong> Puedes corregir o actualizar tu información personal en cualquier momento desde la aplicación</li>
                    <li><strong>Eliminación:</strong> Puedes solicitar la eliminación de tu información personal, sujeto a obligaciones legales</li>
                    <li><strong>Oposición:</strong> Puedes oponerte al procesamiento de tu información personal en ciertas circunstancias</li>
                    <li><strong>Portabilidad:</strong> Puedes solicitar que transfiramos tus datos a otro proveedor de servicios</li>
                    <li><strong>Revocación de consentimiento:</strong> Puedes retirar tu consentimiento en cualquier momento</li>
                </ul>
                <p class="text-gray-700 mt-4 leading-relaxed">
                    Para ejercer estos derechos, puedes contactarnos a través de los medios indicados en 
                    la sección de contacto al final de esta política.
                </p>
            </section>

            <!-- Section 7 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Cookies y Tecnologías Similares</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Nuestra aplicación móvil puede utilizar tecnologías similares a las cookies para:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Mantener tu sesión activa</li>
                    <li>Recordar tus preferencias</li>
                    <li>Mejorar el rendimiento de la aplicación</li>
                    <li>Analizar el uso de la aplicación</li>
                </ul>
                <p class="text-gray-700 mt-4 leading-relaxed">
                    Puedes gestionar estas preferencias a través de la configuración de tu dispositivo 
                    o de la aplicación.
                </p>
            </section>

            <!-- Section 8 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Menores de Edad</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Nuestra aplicación no está dirigida a menores de 18 años. No recopilamos intencionalmente 
                    información personal de menores de edad. Si descubrimos que hemos recopilado información 
                    de un menor sin el consentimiento de sus padres o tutores, tomaremos medidas para 
                    eliminar esa información de nuestros servidores.
                </p>
            </section>

            <!-- Section 9 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Transferencias Internacionales</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Tu información puede ser transferida y almacenada en servidores ubicados fuera de tu 
                    país de residencia. Al utilizar nuestra aplicación, consientes esta transferencia. 
                    Nos comprometemos a garantizar que cualquier transferencia internacional de datos 
                    cumpla con las leyes de protección de datos aplicables.
                </p>
            </section>

            <!-- Section 10 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Cambios a esta Política</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Podemos actualizar esta Política de Privacidad ocasionalmente. Te notificaremos sobre 
                    cambios significativos mediante:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Una notificación en la aplicación</li>
                    <li>Un correo electrónico a la dirección asociada a tu cuenta</li>
                    <li>Una actualización de la fecha de "Última actualización" en esta página</li>
                </ul>
                <p class="text-gray-700 mt-4 leading-relaxed">
                    Te recomendamos revisar esta política periódicamente para estar informado sobre cómo 
                    protegemos tu información.
                </p>
            </section>

            <!-- Section 11 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Contacto</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Si tienes preguntas, inquietudes o solicitudes relacionadas con esta Política de 
                    Privacidad o el manejo de tus datos personales, puedes contactarnos a través de:
                </p>
                <div class="bg-gray-50 rounded-lg p-6 mt-4">
                    <p class="text-gray-700 mb-2">
                        <strong>Email:</strong> privacidad@crm-inmobiliaria.com
                    </p>
                    <p class="text-gray-700 mb-2">
                        <strong>Dirección:</strong> [Dirección de la empresa]
                    </p>
                    <p class="text-gray-700">
                        <strong>Teléfono:</strong> [Número de teléfono]
                    </p>
                </div>
                <p class="text-gray-700 mt-4 leading-relaxed">
                    Nos comprometemos a responder a tus consultas en un plazo razonable.
                </p>
            </section>

            <!-- Section 12 -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Consentimiento</h2>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Al utilizar nuestra aplicación móvil CRM Inmobiliaria, consientes la recopilación, 
                    uso y divulgación de tu información personal de acuerdo con esta Política de Privacidad. 
                    Si no estás de acuerdo con esta política, por favor no utilices nuestra aplicación.
                </p>
            </section>

            <!-- Footer -->
            <div class="mt-10 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-500 text-center">
                    Esta Política de Privacidad es efectiva a partir de {{ date('d/m/Y') }} y se aplica 
                    a todos los usuarios de la aplicación CRM Inmobiliaria.
                </p>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} CRM Inmobiliaria. Todos los derechos reservados.</p>
                <p class="mt-2">
                    <a href="{{ route('welcome') }}" class="text-primary-600 hover:text-primary-700">Inicio</a> | 
                    <a href="{{ route('privacy-policy') }}" class="text-primary-600 hover:text-primary-700">Política de Privacidad</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>

