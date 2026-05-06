<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Art Top Studio - Tienda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="{{ asset('assets/images/art_top/logotrans.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid #eee;
        }

        .product-card {
            border-radius: 12px;
            transition: 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .product-img {
            height: 200px;
            object-fit: cover;
        }

        .btn-dark {
            background-color: #01746D !important;
            border-color: #01746D !important;
        }

        .btn-dark:hover {
            background-color: #015e59 !important;
            border-color: #015e59 !important;
        }

        .btn-light {
            border: 1px solid #01746D;
            color: #01746D;
        }

        .btn-light:hover {
            background-color: #01746D;
            color: #fff;
        }

        .btn-light-locked {
            border: 1px solid #01746D;
            color: #01746D;
            background-color: #fff;
        }

        .btn-light-locked:hover {
            background-color: #eef8f7;
            color: #01746D;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-custom px-4">

    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('shop.index') }}">
        <img src="{{ asset('assets/images/art_top/logotrans.png') }}" width="120" height="70">
        <!-- <strong>Art Top Studio</strong>  MODIFICAR DESPUES -->
        
    </a>

    <li class="nav-item me-4">
    <a class="nav-link" href="{{ route('shop.nosotros') }}">
        Nosotros
    </a>
</li>

<li class="nav-item me-4">
    <a class="nav-link" href="{{ route('shop.contacto') }}">
        Contáctanos/Información
    </a>
</li>

    <div class="ms-auto d-flex align-items-center gap-3">

        @auth
            @if(auth()->user()->role === 'cliente')
                <a href="{{ route('shop.cart') }}" class="btn btn-light">
                    🛒 Carrito
                </a>

                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        👤 {{ auth()->user()->name }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text">
                                <strong>{{ auth()->user()->name }}</strong><br>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="{{ route('shop.profile') }}" class="dropdown-item">
                                👤 Mi perfil
                            </a>
                        </li>    
                        <li>
                            <a class="dropdown-item" href="{{ route('shop.cart') }}">
                                🛒 Ver carrito
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('shop.addresses.index') }}">
                                📍 Direcciones
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('shop.index') }}">
                                🏠 Ir al catálogo
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('inicio') }}" class="btn btn-dark">
                    Panel Admin
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-light-locked">
                🔒 Carrito
            </a>

            <a href="{{ route('login') }}" class="btn btn-dark">
                Iniciar sesión
            </a>
        @endauth

    </div>

</nav>

<div class="container mt-4">
    @yield('content')
</div>

<script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

</body>

</html>