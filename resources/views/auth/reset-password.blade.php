@extends('layouts.shop')

@section('content')
<div class="container py-5">
    <div class="mx-auto" style="max-width: 500px;">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h3 class="mb-2">Restablecer contraseña</h3>
                <p class="text-muted mb-4">
                    Escribe tu nueva contraseña.
                </p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $email) }}"
                                required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password"
                                name="password"
                                class="form-control"
                                required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password"
                                name="password_confirmation"
                                class="form-control"
                                required>
                    </div>

                    <button type="submit"
                            class="btn text-white w-100"
                            style="background-color:#01746D; border-color:#01746D;">
                        Cambiar contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection