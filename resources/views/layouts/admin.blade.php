<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin | @yield('title')</title>

    @vite(['resources/css/ateco.css'])
    @stack('head')
</head>

<body class="bg-gray-100" x-data="{ openSidebar: false }">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- CONTENIDO --}}
    <div class="min-h-screen flex">

        {{-- espacio sidebar --}}
        <div class="w-72 hidden lg:block"></div>

        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>

    @stack('scripts')
</body>
</html>
