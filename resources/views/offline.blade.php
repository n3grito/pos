<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Sin conexión</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-6">
        <div class="text-6xl mb-4">📡</div>
        <h1 class="text-2xl font-bold mb-2">Sin conexión</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6">No hay conexión a Internet. Algunas funciones pueden no estar disponibles.</p>
        <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-colors">Reintentar</button>
    </div>
</body>
</html>
