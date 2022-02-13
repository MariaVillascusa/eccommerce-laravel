<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    {{-- Fontawesome --}}
    {{-- <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}"> --}}

    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

</head>

<body class="font-sans antialiased">
    <x-jet-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            <i class="fa-solid fa-question-circle"></i> <!-- solid style of the question circle icon -->
            <i class="fa-regular fa-question-circle"></i> <!-- regular style of the question circle icon -->
            <i class="fa-light fa-question-circle"></i> <!-- light style of the question circle icon -->

            <i class="fa-brands fa-facebook"></i> <!-- facebook brand icon-->
            <i class="fa-brands fa-facebook-f"></i> <!-- facebook "f" brand icon-->

            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    @stack('scripts')
</body>

</html>
