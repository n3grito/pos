<x-guest-layout>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ __('Política de Privacidad') }}</h1>

        <div class="prose dark:prose-invert text-sm text-gray-600 dark:text-gray-400 space-y-4">
            <p>Esta política de privacidad describe cómo se recopila, utiliza y protege la información personal de los usuarios.</p>

            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Información que recopilamos</h2>
            <p>Recopilamos la información que nos proporciona al registrarse, incluyendo nombre, correo electrónico y datos necesarios para la operación del sistema POS.</p>

            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Uso de cookies</h2>
            <p>Utilizamos cookies esenciales para el funcionamiento del sistema:</p>
            <ul class="list-disc pl-5 space-y-1">
                <li><strong>Cookie de sesión (laravel_session):</strong> Necesaria para mantener su sesión iniciada.</li>
                <li><strong>Cookie XSRF-TOKEN:</strong> Necesaria para proteger contra ataques CSRF.</li>
            </ul>
            <p>No utilizamos cookies de terceros ni cookies de rastreo.</p>

            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Uso de la información</h2>
            <p>La información recopilada se utiliza únicamente para la operación del sistema: gestión de ventas, inventario, clientes y usuarios autorizados.</p>

            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Protección de datos</h2>
            <p>Implementamos medidas de seguridad técnicas y organizativas para proteger su información personal contra acceso no autorizado, alteración o divulgación.</p>

            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Contacto</h2>
            <p>Para cualquier consulta sobre esta política de privacidad, puede contactarnos a través de los medios proporcionados en el sistema.</p>

            <p class="text-xs text-gray-400 dark:text-gray-500 mt-8">Última actualización: junio 2026</p>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ url('/') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">&larr; Volver al inicio</a>
        </div>
    </div>
</x-guest-layout>
