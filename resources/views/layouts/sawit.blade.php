<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebun Sawit - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            min-height: 100vh;
        }
        .navbar-brand { font-weight: 800; font-size: 1.4rem; }
        .coin-badge {
            background: linear-gradient(135deg, #f9a825, #f57f17);
            color: white;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(245,127,23,0.4);
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        }
        .card-header {
            border-radius: 16px 16px 0 0 !important;
            font-weight: 700;
        }
        .btn { font-weight: 600; border-radius: 10px; }
        .stat-card {
            text-align: center;
            padding: 24px 16px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }
        .nav-link { font-weight: 600; }
        .nav-link.active {
            background: rgba(255,255,255,0.3) !important;
            border-radius: 8px;
        }
        .alert { border-radius: 12px; border: none; }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark" style="background: linear-gradient(135deg, #2e7d32, #1b5e20); box-shadow: 0 2px 12px rgba(0,0,0,0.2);">
    <div class="container">
        <a class="navbar-brand" href="{{ route('sawit.kebun') }}">Kebun Sawit</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sawit.kebun') ? 'active' : '' }}"
                       href="{{ route('sawit.kebun') }}">Kebun Sawitku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sawit.berkebun') ? 'active' : '' }}"
                       href="{{ route('sawit.berkebun') }}">Berkebun</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="coin-badge"> {{ number_format(Auth::user()->coin) }} Coin</span>

                <!-- profile dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img 
                            src="{{ Auth::user()->profile_picture_url }}" 
                            alt="profile"
                            width="32"
                            height="32"
                            class="rounded-circle me-2"
                            style="object-fit: cover;"
                        >
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Change Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container py-4">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>