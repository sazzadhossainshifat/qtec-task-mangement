<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Task Manager - Manage your team's daily work efficiently">
    <title>{{ $title ?? config('app.name', 'Task Manager') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS (via CDN for utilities) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false }, // Don't reset Bootstrap styles
        }
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: linear-gradient(160deg, #1e293b 0%, #0f172a 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-brand .brand-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.02em;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
        }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #475569;
            padding: 0.5rem 1.25rem;
            margin-top: 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.2s;
            text-decoration: none;
            margin: 0.1rem 0.5rem;
            border-radius: 0.5rem;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.07);
            color: #f1f5f9;
        }

        .sidebar-nav .nav-link.active {
            background: rgba(59,130,246,0.2);
            color: #60a5fa;
            font-weight: 600;
        }

        .sidebar-nav .nav-link .icon {
            font-size: 1rem;
            width: 1.25rem;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .sidebar-user .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-user .user-info .name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #e2e8f0;
            line-height: 1.2;
        }

        .sidebar-user .user-info .email {
            font-size: 0.68rem;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }

        /* Main Content */
        .main-wrapper {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .top-navbar .page-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }

        /* Content area */
        .content-area {
            padding: 1.75rem 1.5rem;
            flex: 1;
        }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 0.875rem;
            padding: 1.25rem 1.5rem;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.07);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 0.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1;
            margin-top: 0.75rem;
        }

        .stat-card .stat-label {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        /* Task table / cards */
        .task-card {
            background: #fff;
            border-radius: 0.875rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .task-card .card-header-custom {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .task-card .card-header-custom h2 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        /* Status badges */
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3em 0.65em;
            border-radius: 0.375rem;
        }

        .badge-in_progress {
            background: #dbeafe;
            color: #1e40af;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3em 0.65em;
            border-radius: 0.375rem;
        }

        .badge-completed {
            background: #d1fae5;
            color: #065f46;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3em 0.65em;
            border-radius: 0.375rem;
        }

        /* Priority badges */
        .badge-low    { background: #f0fdf4; color: #166534; font-size: 0.7rem; font-weight: 600; padding: 0.3em 0.65em; border-radius: 0.375rem; }
        .badge-medium { background: #fff7ed; color: #9a3412; font-size: 0.7rem; font-weight: 600; padding: 0.3em 0.65em; border-radius: 0.375rem; }
        .badge-high   { background: #fff1f2; color: #9f1239; font-size: 0.7rem; font-weight: 600; padding: 0.3em 0.65em; border-radius: 0.375rem; }

        /* Alert flash */
        .flash-alert {
            border-radius: 0.625rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Form controls */
        .form-control, .form-select {
            font-size: 0.875rem;
            border-radius: 0.5rem;
            border-color: #e2e8f0;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
        }

        /* Buttons */
        .btn-primary {
            background: #3b82f6;
            border-color: #3b82f6;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
        }

        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-outline-secondary {
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
        }

        /* Table */
        .table th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 2px solid #f1f5f9;
            padding: 0.75rem 1rem;
            background: #f8fafc;
        }

        .table td {
            font-size: 0.875rem;
            color: #374151;
            padding: 0.85rem 1rem;
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        .table tr:hover td {
            background: #f8fafc;
        }

        /* Responsive: Mobile sidebar toggle */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div>
            <div class="brand-sub">Qtec Solution</div>
            <div class="brand-text">
                <i class="bi bi-check2-square me-2 text-blue-400"></i>Task Manager
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Navigation</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           id="nav-dashboard">
            <span class="icon"><i class="bi bi-grid-1x2-fill"></i></span>
            Dashboard
        </a>

        <a href="{{ route('tasks.index') }}"
           class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}"
           id="nav-tasks">
            <span class="icon"><i class="bi bi-list-task"></i></span>
            My Tasks
        </a>

        <a href="{{ route('tasks.create') }}"
           class="nav-link {{ request()->routeIs('tasks.create') ? 'active' : '' }}"
           id="nav-new-task">
            <span class="icon"><i class="bi bi-plus-circle-fill"></i></span>
            New Task
        </a>

        <div class="sidebar-section-label mt-3">Account</div>

        <a href="{{ route('profile.edit') }}"
           class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
           id="nav-profile">
            <span class="icon"><i class="bi bi-person-fill"></i></span>
            Profile
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-100 border-0 text-start" id="nav-logout"
                    style="background:none; cursor:pointer;">
                <span class="icon"><i class="bi bi-box-arrow-right"></i></span>
                Logout
            </button>
        </form>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="email">{{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- Main Content -->
<div class="main-wrapper">
    <!-- Top Navbar -->
    <header class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <!-- Mobile toggle -->
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <span class="page-title">{{ $title ?? 'Dashboard' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-new-task-top">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-sm-inline">New Task</span>
            </a>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mx-4 mt-3">
            <div class="alert alert-success alert-dismissible flash-alert d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mx-4 mt-3">
            <div class="alert alert-danger alert-dismissible flash-alert d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="content-area">
        {{ $slot }}
    </main>

    <footer class="text-center py-3" style="color: #94a3b8; font-size: 0.75rem; border-top: 1px solid #e2e8f0;">
        &copy; {{ date('Y') }} Task Manager — Qtec Solution
    </footer>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Mobile sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('open');
    });

    // Auto-dismiss alerts after 4 seconds
    setTimeout(function () {
        document.querySelectorAll('.flash-alert').forEach(function (el) {
            var alert = bootstrap.Alert.getOrCreateInstance(el);
            alert.close();
        });
    }, 4000);
</script>

@stack('scripts')
</body>
</html>
