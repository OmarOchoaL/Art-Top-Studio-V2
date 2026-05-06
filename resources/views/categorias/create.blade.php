@extends('app')

@section('content')

<div class="container mt-1">
    <h3>Nueva Categoría</h3>

    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="d-flex justify-content-center">
        <button type="submit" class="btn text-white btn-lg px-3" style="background-color:#01746D; border-color:#01746D;">Guardar</button>
        </div>
    </form>
</div>

@endsection
