<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    {{-- Sidebar --}}
    <x-sidebar />

    {{-- Content --}}
    <main class="ml-64 p-8">
       @yield('content')
    </main>

</body>
</html>
