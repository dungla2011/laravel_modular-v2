# Base Module Views System

This directory contains the view system for the Base module, providing reusable layouts, components, and partials for all other modules in the application.

## Structure

```
Views/
├── layouts/              # Base layouts
│   ├── app.blade.php     # Main application layout
│   └── admin.blade.php   # Admin panel layout
├── components/           # Reusable components
│   ├── card.blade.php    # Card component
│   ├── data-table.blade.php # Data table component
│   ├── form.blade.php    # Form component
│   ├── modal.blade.php   # Modal component
│   └── stats.blade.php   # Statistics component
└── partials/             # Shared partials
    ├── alerts.blade.php  # Alert messages
    ├── navigation.blade.php # Main navigation
    └── admin/            # Admin-specific partials
        ├── header.blade.php # Admin header
        └── sidebar.blade.php # Admin sidebar
```

## Usage

### 1. Extending Layouts

**App Layout:**
```blade
@extends('Base::layouts.app')

@section('title', 'Page Title')

@section('content')
    <div class="container">
        <h1>Welcome to my module</h1>
    </div>
@endsection
```

**Admin Layout:**
```blade
@extends('Base::layouts.admin')

@section('page-title', 'User Management')
@section('page-description', 'Manage system users')

@section('content')
    <!-- Your admin content here -->
@endsection
```

### 2. Using Components

**Data Table:**
```blade
@include('Base::components.data-table', [
    'title' => 'Users',
    'endpoint' => '/api/admin/users',
    'columns' => [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'status', 'label' => 'Status', 'format' => 'status'],
    ],
    'actions' => [
        ['key' => 'edit', 'label' => 'Edit', 'icon' => 'fas fa-edit', 'url' => '/admin/users/:id/edit'],
        ['key' => 'delete', 'label' => 'Delete', 'icon' => 'fas fa-trash', 'url' => '/admin/users/:id', 'method' => 'DELETE', 'confirm' => 'Are you sure?'],
    ],
    'createRoute' => '/admin/users/create'
])
```

**Form Component:**
```blade
@include('Base::components.form', [
    'endpoint' => '/api/admin/users',
    'method' => 'POST',
    'redirect' => '/admin/users',
    'fields' => [
        ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
        ['name' => 'email', 'type' => 'email', 'label' => 'Email', 'required' => true],
        ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive']
        ]],
    ]
])
```

**Modal Component:**
```blade
@include('Base::components.modal', [
    'name' => 'user-modal',
    'title' => 'User Details',
    'triggerButton' => 'View User',
    'triggerIcon' => 'fas fa-eye'
])
    <p>User information goes here...</p>
    
    @slot('footer')
        <button @click="closeModal('user-modal')" class="btn btn-primary">Close</button>
    @endslot
@endinclude
```

**Card Component:**
```blade
@include('Base::components.card', [
    'title' => 'User Statistics',
    'subtitle' => 'Overview of user activity'
])
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="text-2xl font-bold">1,234</span>
            <p class="text-gray-500">Total Users</p>
        </div>
        <div>
            <span class="text-2xl font-bold">567</span>
            <p class="text-gray-500">Active Users</p>
        </div>
    </div>
    
    @slot('actions')
        <button class="btn btn-primary">View Report</button>
    @endslot
@endinclude
```

**Stats Component:**
```blade
@include('Base::components.stats', [
    'columns' => '4',
    'stats' => [
        [
            'label' => 'Total Users',
            'value' => '1,234',
            'icon' => 'fas fa-users',
            'iconBg' => 'bg-blue-500',
            'change' => '12%',
            'changeType' => 'increase',
            'link' => ['url' => '/admin/users', 'text' => 'View all users']
        ],
        [
            'label' => 'Revenue',
            'value' => '$45,678',
            'icon' => 'fas fa-dollar-sign',
            'iconBg' => 'bg-green-500',
            'change' => '8%',
            'changeType' => 'increase'
        ]
    ]
])
```

### 3. Including Partials

**Alerts:**
```blade
@include('Base::partials.alerts')
```

**Navigation:**
```blade
@include('Base::partials.navigation')
```

## Features

### Layout Features
- **Responsive Design**: All layouts are mobile-friendly
- **Dark Mode Support**: Ready for dark mode implementation
- **SEO Optimized**: Proper meta tags and structure
- **Performance**: Optimized CSS/JS loading

### Component Features
- **API Integration**: Components work with the Base module's API helpers
- **Real-time Updates**: AJAX-powered data loading
- **Validation**: Built-in form validation
- **Accessibility**: ARIA labels and keyboard navigation
- **Customizable**: Extensive configuration options

### Admin Layout Features
- **Collapsible Sidebar**: Space-saving navigation
- **Breadcrumbs**: Easy navigation tracking
- **User Menu**: Profile and logout options
- **Notifications**: Alert system integration
- **Module Navigation**: Extensible menu system

## Customization

### Adding Module-Specific Sidebar Items

In your module's admin views, extend the admin navigation:

```blade
@extends('Base::layouts.admin')

@section('admin-nav-items')
    <a href="{{ route('admin.news.index') }}" 
       class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
        <i class="fas fa-newspaper w-5"></i>
        <span x-show="!collapsed" class="ml-3">News</span>
    </a>
@endsection
```

### Styling Components

Components use Tailwind CSS classes and can be customized by:

1. **Override Classes**: Pass custom classes via the `class` parameter
2. **Extend Components**: Create new components that extend base ones
3. **Custom CSS**: Add module-specific styles in the `@push('styles')` section

### JavaScript Integration

Components integrate with the Base module's JavaScript helpers:

- **API Helper**: `window.adminApi` for AJAX calls
- **UI Helper**: `showSuccess()`, `showError()`, `confirmDelete()`
- **Modal Helper**: `openModal()`, `closeModal()`

## Best Practices

1. **Use Components**: Prefer components over custom HTML for consistency
2. **Extend Layouts**: Always extend base layouts rather than creating new ones
3. **API-First**: Use API endpoints for all data operations
4. **Validation**: Implement both client-side and server-side validation
5. **Accessibility**: Ensure all components are accessible
6. **Performance**: Lazy-load data when possible
7. **Responsive**: Test on mobile devices

## Module Integration

When creating a new module, follow this pattern:

```php
// In your module's ViewServiceProvider
namespace Modules\YourModule\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register module views
        View::addNamespace('YourModule', module_path('YourModule', 'Views'));
        
        // Share common data with all views
        View::composer('YourModule::*', function ($view) {
            // Add common view data
        });
    }
}
```

This allows your module views to extend Base layouts:

```blade
@extends('Base::layouts.admin')
<!-- Your module content -->
```
