@extends('app')

@section('content')

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h3 class="mb-4">Editar Categoría</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $categoria->name) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description"
                            class="form-control"
                            rows="3">{{ old('description', $categoria->description) }}</textarea>
                </div>
                <div class="card p-4">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="submit"
                                class="btn btn-lg text-white px-4"
                                style="background-color:#01746D; border-color:#01746D;">
                            Actualizar
                        </button>
                        <a href="{{ route('categorias.index') }}"
                        class="btn btn-lg text-white px-4"
                        style="background-color:red; border-color:red;">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
