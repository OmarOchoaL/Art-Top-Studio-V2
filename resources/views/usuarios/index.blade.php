@extends('app')

@section('content')

<div class="container mt-3">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Usuarios</h3>

        <a href="{{ route('usuarios.create') }}"
           class="btn text-white"
           style="background-color:#01746D; border-color:#01746D;">
            Nuevo Usuario
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            @if($usuarios->count() === 0)
                <p class="text-muted mb-0">No hay usuarios registrados.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th width="180">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr>
                                <td>#{{ $usuario->id }}</td>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->phone ?? 'Sin teléfono' }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    @if($usuario->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($usuario->role === 'colaborador')
                                        <span class="badge bg-warning">Colaborador</span>
                                    @else
                                        <span class="badge bg-primary">Cliente</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-warning w-100 mb-2"
                                            onclick="abrirConfirmacionUsuario(
                                                'edit',
                                                '{{ route('usuarios.verifyEdit', $usuario->id) }}',
                                                @js($usuario->name)
                                            )">
                                        Editar
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-danger w-100"
                                            onclick="abrirConfirmacionUsuario(
                                                'delete',
                                                '{{ route('usuarios.destroy', $usuario->id) }}',
                                                @js($usuario->name)
                                            )">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>

</div>

<div id="confirmacionUsuarioOverlay"
     style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,0.08);
        z-index:9999;
        align-items:center;
        justify-content:center;
     ">

    <div style="
        width:100%;
        max-width:420px;
        background:#fff;
        border-radius:12px;
        box-shadow:0 10px 30px rgba(0,0,0,0.18);
        padding:24px;
     ">
        <h5 id="confirmacionUsuarioTitulo" class="mb-3">Confirmar acción</h5>

        <p id="confirmacionUsuarioTexto" class="mb-3 text-muted"></p>

        <form id="confirmacionUsuarioForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="confirmacionMetodo" value="POST">

            <div class="mb-3">
                <label for="admin_password_modal" class="form-label fw-bold">
                    Contraseña de administrador
                </label>
                <input type="password"
                       name="admin_password"
                       id="admin_password_modal"
                       class="form-control"
                       autocomplete="off"
                       required>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button"
                        class="btn btn-secondary"
                        onclick="cerrarConfirmacionUsuario()">
                    Cancelar
                </button>

                <button type="submit"
                        id="confirmacionUsuarioBoton"
                        class="btn text-white"
                        style="background-color:#01746D; border-color:#01746D;">
                    Continuar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirConfirmacionUsuario(tipo, actionUrl, userName) {
        const overlay = document.getElementById('confirmacionUsuarioOverlay');
        const form = document.getElementById('confirmacionUsuarioForm');
        const metodo = document.getElementById('confirmacionMetodo');
        const titulo = document.getElementById('confirmacionUsuarioTitulo');
        const texto = document.getElementById('confirmacionUsuarioTexto');
        const boton = document.getElementById('confirmacionUsuarioBoton');
        const password = document.getElementById('admin_password_modal');

        form.action = actionUrl;
        password.value = '';

        if (tipo === 'edit') {
            metodo.value = 'POST';
            titulo.innerText = 'Confirmar edición';
            texto.innerHTML = `Vas a editar a <strong>${userName}</strong>. Escribe tu contraseña para continuar.`;
            boton.innerText = 'Continuar';
            boton.style.backgroundColor = '#01746D';
            boton.style.borderColor = '#01746D';
        } else {
            metodo.value = 'DELETE';
            titulo.innerText = 'Confirmar eliminación';
            texto.innerHTML = `Vas a eliminar a <strong>${userName}</strong>. Escribe tu contraseña para continuar.`;
            boton.innerText = 'Eliminar';
            boton.style.backgroundColor = '#dc3545';
            boton.style.borderColor = '#dc3545';
        }

        overlay.style.display = 'flex';

        setTimeout(() => {
            password.focus();
        }, 50);
    }

    function cerrarConfirmacionUsuario() {
        const overlay = document.getElementById('confirmacionUsuarioOverlay');
        const password = document.getElementById('admin_password_modal');

        overlay.style.display = 'none';
        password.value = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.getElementById('confirmacionUsuarioOverlay');

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                cerrarConfirmacionUsuario();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                cerrarConfirmacionUsuario();
            }
        });
    });
</script>

@endsection