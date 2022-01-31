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
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    {{-- Sweetalert2 --}}
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.css') }}">
    {{-- Dropzone --}}
    {{-- <link rel="stylesheet" href="{{ asset('vendor/dropzone/dist/dropzone.css') }}"> --}}

    @livewireStyles


    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    {{-- Ckeditor --}}
    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
    {{-- Sweetalert2 --}}
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.js') }}"></script>
    {{-- Dropzone --}}
    {{-- <script src="{{ asset('vendor/dropzone/dist/dropzone.js') }}"></script> --}}

    {{-- Dropzone --}}
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

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
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    @stack('scripts')
</body>

</html>
