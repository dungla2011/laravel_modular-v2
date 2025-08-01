<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Admin Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')
    @hasSection('module-styles')
        @yield('module-styles')
    @endif

    <style>
        .sidebar-transition { transition: all 0.3s ease; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('Base::partials.admin.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            @include('Base::partials.admin.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <!-- Breadcrumbs -->
                @hasSection('breadcrumbs')
                    <div class="bg-white border-b px-6 py-4">
                        @yield('breadcrumbs')
                    </div>
                @endif

                <!-- Page Header -->
                @hasSection('page-header')
                    <div class="bg-white border-b px-6 py-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title')</h1>
                                @hasSection('page-description')
                                    <p class="mt-1 text-sm text-gray-600">@yield('page-description')</p>
                                @endif
                            </div>
                            @hasSection('page-actions')
                                <div class="flex space-x-3">
                                    @yield('page-actions')
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Alerts -->
                @include('Base::partials.alerts')

                <!-- Main Content -->
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('modules/Base/Assets/api-helper.js') }}"></script>
    
    @stack('scripts')
    @hasSection('module-scripts')
        @yield('module-scripts')
    @endif

    <!-- Admin specific scripts -->
    <script>
        // Global API instance for admin
        window.adminApi = new ApiHelper('/api/admin', '{{ csrf_token() }}');
        
        // Common admin functions
        window.confirmDelete = async (message = 'Are you sure you want to delete this item?') => {
            return await UiHelper.confirm(message);
        };

        window.bulkDelete = async (moduleName, selectedIds) => {
            if (selectedIds.length === 0) {
                showError('Please select items to delete');
                return;
            }
            
            if (await confirmDelete(`Delete ${selectedIds.length} selected items?`)) {
                try {
                    const crud = new ModuleCrud(`admin/${moduleName}`, adminApi);
                    await crud.bulkDelete(selectedIds);
                    showSuccess(`Successfully deleted ${selectedIds.length} items`);
                    location.reload();
                } catch (error) {
                    showError(error.message);
                }
            }
        };

        window.toggleStatus = async (moduleName, id) => {
            try {
                const crud = new ModuleCrud(`admin/${moduleName}`, adminApi);
                await crud.toggleStatus(id);
                showSuccess('Status updated successfully');
                location.reload();
            } catch (error) {
                showError(error.message);
            }
        };

        // Initialize tooltips, modals, etc.
        document.addEventListener('DOMContentLoaded', function() {
            // Add any admin-specific initialization here
        });
    </script>
</body>
</html>
