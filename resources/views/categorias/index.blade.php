@extends('app')

@section('content')

<div class="container mt-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h3>Lista de Categorías</h3>
        <a href="{{ route('categorias.create') }}"  
        class="btn text-white" 
        style="background-color:#01746D; border-color:#01746D;">
            Nueva Categoría
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->name }}</td>
                            <td>{{ $categoria->description }}</td>
                            <td>
                                <a href="{{ route('categorias.edit', $categoria->id) }}" 
                                class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                                <form action="{{ route('categorias.destroy', $categoria->id) }}" 
                                    method="POST" 
                                    class="d-inline"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm w-100 btn-danger">
                                        Eliminar
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection
