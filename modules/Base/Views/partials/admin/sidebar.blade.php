<div class="bg-gray-800 text-white w-64 min-h-screen px-4 py-6 sidebar-transition" x-data="{ collapsed: false }">
    <!-- Logo -->
    <div class="flex items-center mb-8">
        <div class="text-xl font-bold">
            <span x-show="!collapsed">Admin Panel</span>
            <span x-show="collapsed" class="text-center w-full block">AP</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard', [], false) ?? '/admin' }}" 
           class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition duration-200">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span x-show="!collapsed" class="ml-3">Dashboard</span>
        </a>

        <!-- Module Navigation -->
        @yield('admin-nav-items')

        <!-- Default Admin Items -->
        <div class="border-t border-gray-700 pt-4 mt-4">
            <!-- Users Management -->
            <a href="{{ route('admin.users.index', [], false) ?? '#' }}" 
               class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition duration-200">
                <i class="fas fa-users w-5"></i>
                <span x-show="!collapsed" class="ml-3">Users</span>
            </a>

            <!-- Settings -->
            <a href="{{ route('admin.settings.index', [], false) ?? '#' }}" 
               class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition duration-200">
                <i class="fas fa-cog w-5"></i>
                <span x-show="!collapsed" class="ml-3">Settings</span>
            </a>

            <!-- System Info -->
            <a href="{{ route('admin.system.info', [], false) ?? '#' }}" 
               class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition duration-200">
                <i class="fas fa-info-circle w-5"></i>
                <span x-show="!collapsed" class="ml-3">System Info</span>
            </a>
        </div>
    </nav>

    <!-- Collapse Button -->
    <div class="absolute bottom-4 left-4">
        <button @click="collapsed = !collapsed" 
                class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition duration-200">
            <i class="fas fa-bars w-5"></i>
            <span x-show="!collapsed" class="ml-3">Collapse</span>
        </button>
    </div>
</div>
