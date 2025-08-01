<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @stack('styles')

    <!-- Module specific styles -->
    @hasSection('module-styles')
        @yield('module-styles')
    @endif
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        @include('base::partials.navigation')

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            <!-- Alerts -->
            @include('base::partials.alerts')

            <!-- Breadcrumbs -->
            @hasSection('breadcrumbs')
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    @yield('breadcrumbs')
                </div>
            @endif

            <!-- Main Content -->
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Footer -->
        @include('base::partials.footer')
    </div>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <!-- Base API Helper -->
    <script src="{{ asset('modules/Base/Assets/api-helper.js') }}"></script>
    
    @stack('scripts')

    <!-- Module specific scripts -->
    @hasSection('module-scripts')
        @yield('module-scripts')
    @endif

    <!-- Initialize API Helper -->
    <script>
        // Global API instance
        window.api = new ApiHelper('/api', '{{ csrf_token() }}');
        
        // Global UI Helper functions
        window.showSuccess = (message) => UiHelper.showSuccess(message);
        window.showError = (message, errors = null) => UiHelper.showError(message, errors);
        window.confirm = (message) => UiHelper.confirm(message);
    </script>
</body>
</html>
