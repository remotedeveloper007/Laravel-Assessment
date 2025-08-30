<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">    

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .nav-avatar { width:32px; height:32px; border-radius:50%; object-fit:cover; }
        .badge-online { background:#28a745; }
        .badge-offline { background:#6c757d; }
        main { padding-top:1rem; }
    </style>

    @stack('head')
</head>
<body>
    <div id="app">
        <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm"> -->
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name','App') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>               

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @if(Auth::guard('admin')->check())
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            
                        @elseif(Auth::guard('customer')->check())
                            <li class="nav-item"><a class="nav-link" href="{{ route('customer.dashboard') }}">My Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}">Shop</a></li>
                        @endif
                    </ul>

                    <ul class="navbar-nav ms-auto">
                    @if(Auth::guard('admin')->check() || Auth::guard('customer')->check())
                        @php
                        $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('customer')->user();
                        $guard = Auth::guard('admin')->check() ? 'admin' : 'customer';
                        @endphp
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navUser" role="button" data-bs-toggle="dropdown">
                            <img src="{{ asset('storage/'.($user->avatar ?? 'defaults/user.png')) }}" alt="avatar" class="nav-avatar me-2">
                            <span>{{ $user->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-item-text">
                            <small class="text-muted">Signed in as</small><br>
                            <strong>{{ $user->email }}</strong><br>
                            <span class="small">
                                @if($user->online)
                                <span class="badge badge-online">Online</span>
                                @else
                                <span class="badge badge-offline">Offline</span>
                                @endif
                            </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                            <form method="POST" action="{{ $guard === 'admin' ? route('admin.logout') : route('customer.logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                            </li>
                        </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('customer.login') }}">Customer Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.login') }}">Admin Login</a></li>
                    @endif
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <!-- <ul class="navbar-nav ms-auto">
                        
                        @guest
                            @if (Route::has('admin.login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.login') }}">{{ __('Admin Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('customer.login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('customer.login') }}">{{ __('Customer Login') }}</a>
                                </li>
                            @endif
                        @else
                            @php
                                $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::guard('customer')->user();
                                $guard = Auth::guard('admin')->check() ? 'admin' : 'customer';
                            @endphp     
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <img src="{{ asset('storage/'.($user->avatar ?? 'defaults/user.png')) }}" alt="avatar" class="nav-avatar me-2">
                                    <span> {{ Auth::user()->name }}</span>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-item-text">
                                    <small class="text-muted">Signed in as</small><br>
                                    <strong>{{ $user->email }}</strong><br>
                                    <span class="small">
                                        @if($user->online)
                                        <span class="badge badge-online">Online</span>
                                        @else
                                        <span class="badge badge-offline">Offline</span>
                                        @endif
                                    </span>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ $guard === 'admin' ? route('admin.logout') : route('customer.logout') }}"
                                            onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ $guard === 'admin' ? route('admin.logout') : route('customer.logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>

                            </li>
                        @endguest
                    </ul>  -->
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <footer class="bg-light py-3 mt-5">
            <div class="container text-center small text-muted">© {{ date('Y') }} {{ config('app.name') }}</div>
        </footer>        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    </script>
    @stack('scripts')    
</body>
</html>
