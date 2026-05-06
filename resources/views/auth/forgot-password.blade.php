@extends('layouts.shop')

@section('content')
<div class="container py-5">
    <div class="mx-auto" style="max-width: 500px;">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h3 class="mb-2">¿Olvidaste tu contraseña?</h3>
                <p class="text-muted mb-4">
                    Escribe el correo con el que te registraste y te enviaremos un enlace para restablecerla.
                </p>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                required>
                    </div>

                    <button type="submit"
                            class="btn text-white w-100"
                            style="background-color:#01746D; border-color:#01746D;">
                        Enviar enlace de recuperación
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection