@extends('layouts.shop')

@section('content')
<div class="container py-4">
    <style>
        .profile-wrapper {
            max-width: 950px;
            margin: 0 auto;
        }

        .profile-header {
            background: #fff;
            border-radius: 20px;
            padding: 22px 24px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            margin-bottom: 24px;
        }

        .profile-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .profile-subtitle {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .profile-card {
            background: #fff;
            border: none;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .profile-card .card-body {
            padding: 28px;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 18px;
        }

        .form-label-custom {
            font-size: 0.92rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-control-custom {
            height: 48px;
            border-radius: 14px;
            border: 1px solid #d1d5db;
            padding: 0 14px;
            box-shadow: none !important;
        }

        .form-control-custom:focus {
            border-color: #01746D;
            box-shadow: 0 0 0 0.2rem rgba(1, 116, 109, 0.12) !important;
        }

        .btn-brand {
            background-color: #01746D;
            border-color: #01746D;
            color: #fff;
            border-radius: 14px;
            height: 48px;
            font-weight: 600;
        }

        .btn-brand:hover {
            background-color: #015d58;
            border-color: #015d58;
            color: #fff;
        }
    </style>

    <div class="profile-wrapper">
        <div class="profile-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="profile-title">Mi perfil</h3>
                <p class="profile-subtitle">Actualiza tus datos personales y tu contraseña.</p>
            </div>

            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                Volver a la tienda
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card profile-card">
            <div class="card-body">
                <h5 class="section-title">Editar información</h5>

                <form action="{{ route('shop.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">Nombre</label>
                            <input type="text"
                                   name="name"
                                   class="form-control form-control-custom"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">Teléfono</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control form-control-custom"
                                   value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label form-label-custom">Correo electrónico</label>
                            <input type="email"
                                   name="email"
                                   class="form-control form-control-custom"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">Nueva contraseña</label>
                            <input type="password"
                                   name="password"
                                   class="form-control form-control-custom"
                                   placeholder="Déjalo vacío si no quieres cambiarla">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-custom">Confirmar contraseña</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control form-control-custom"
                                   placeholder="Repite la nueva contraseña">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100">
                        Guardar cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection