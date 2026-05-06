<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <title>Art Top || Login</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/images/art_top/logo.jpg">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
</head>

<body>

<main class="auth-creative-wrapper">
<div class="auth-creative-inner">
<div class="creative-card-wrapper">
<div class="card my-4 overflow-hidden" style="z-index: 1">
<div class="row flex-1 g-0">

<div class="col-lg-6 h-100 my-auto order-1 order-lg-0">

<div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-50 start-50 d-none d-lg-block">
    <img src="assets/images/art_top/avatar.png" alt="" class="img-fluid">
</div>

<div class="creative-card-body card-body p-sm-5">

<h1 class="fs-20 fw-bolder mb-4">Iniciar sesión</h1>
<h4 class="fs-13 fw-bold mb-2">Iniciar sesión con tu cuenta</h4>

{{-- ERROR LOGIN --}}
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- NUEVO: ERRORES VALIDACIÓN --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- NUEVO: MENSAJE SUCCESS --}}
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('login.store') }}" class="w-100 mt-4 pt-2">
@csrf

<div class="mb-4">
    <input type="email" 
        name="email"
        value="{{ old('email') }}" {{-- NUEVO --}}
        class="form-control" 
        placeholder="Email" 
        required>
</div>

<div class="mb-3">
    <input type="password" 
        name="password"
        class="form-control" 
        placeholder="Password" 
        required>
</div>

<div class="mt-5">
    <button type="submit" 
    class="btn btn-lg w-100" 
    style="background-color:#01746D; color:white">
    Iniciar sesión
</button>
</div>

</form>

{{-- NUEVO: LINKS CLIENTE --}}
<div class="text-center mt-4">

    <p class="mb-2">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" style="color:#01746D; font-weight:bold;">
            Crear cuenta
        </a>
    </p>

    <div class="text-center mt-3">
    <a href="{{ route('password.request') }}" class="text-decoration-none">
        ¿Olvidaste tu contraseña?
    </a>
    </div>
    <hr>
    <p class="mb-0">
        <a href="{{ route('shop.index') }}" class="text-muted">
            Volver a la tienda
        </a>
    </p>

</div>

</div>
</div>

<div class="col-lg-6 p-0 order-0 order-lg-1">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <img src="assets/images/art_top/logo4k.jpg" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
    </div>
</div>

</div>
</div>
</div>
</div>
</main>

<script src="assets/vendors/js/vendors.min.js"></script>
<script src="assets/js/common-init.min.js"></script>
<script src="assets/js/theme-customizer-init.min.js"></script>

</body>
</html>