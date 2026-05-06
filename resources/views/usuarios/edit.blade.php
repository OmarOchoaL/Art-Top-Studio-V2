@extends('app')

@section('content')

<div class="container mt-3">

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color:#01746D;">
                    <h4 class="mb-0 text-white">Editar Usuario</h4>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $usuario->name) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone', $usuario->phone) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo electrónico</label>
                            <input type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email', $usuario->email) }}"
                                    required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva contraseña</label>
                            <input type="password"
                                    name="password"
                                    class="form-control">
                            <small class="text-muted">
                                Déjala vacía si no quieres cambiarla.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmar nueva contraseña</label>
                            <input type="password"
                                    name="password_confirmation"
                                    class="form-control">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Rol</label>
                            <select name="role" class="form-select" required>
                                <option value="admin"
                                    {{ old('role', $usuario->role) == 'admin' ? 'selected' : '' }}>
                                    Administrador
                                </option>

                                <option value="colaborador"
                                    {{ old('role', $usuario->role) == 'colaborador' ? 'selected' : '' }}>
                                    Colaborador
                                </option>

                                <option value="cliente"
                                    {{ old('role', $usuario->role) == 'cliente' ? 'selected' : '' }}>
                                    Cliente
                                </option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('usuarios.index') }}"
                                class="btn btn-secondary">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="btn text-white"
                                    style="background-color:#01746D; border-color:#01746D;">
                                Actualizar Usuario
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection