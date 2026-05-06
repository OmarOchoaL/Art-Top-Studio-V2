@extends('app')

@section('content')

<div class="container mt-2">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h3>Lista de Productos</h3>
        <a href="{{ route('productos.create') }}"
            class="btn text-white"
            style="background-color:#01746D; border-color:#01746D;">
            Nuevo Producto
        </a>
    </div>

                                <div class="card shadow-sm">
                                    <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Tamaño</th>
                        <th>Colores</th>
                        <th width="160">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($productos as $producto)
                        <tr>

                            <td>
                                @if($producto->image)
                                    <img src="{{ asset('images/products/'.$producto->image) }}"
                                        width="60"
                                        class="rounded">
                                @else
                                    <span class="text-muted">Sin imagen</span>
                                @endif
                            </td>

                            <td>{{ $producto->name }}</td>

                            <td>${{ number_format($producto->price, 2) }}</td>

                            <td>
                                {{ $producto->category->name ?? 'Sin categoría' }}
                            </td>

                            <td>
                                {{ $producto->quantity }}
                            </td>

                            <td>
                                @if($producto->size)
                                    {{ $producto->size }}
                                @else
                                    <span class="text-muted">Sin tamaños</span>
                                @endif
                            </td>

                            <td>
                                
                                @if($producto->colores->count())
                                    <div class="d-flex flex-wrap gap-2">

                                        @foreach($producto->colores as $color)
                                            <div title="{{ $color->nombre }}"
                                                style="
                                                    width:20px;
                                                    height:20px;
                                                    border-radius:50%;
                                                    border:1px solid #ccc;
                                                    background-color: {{ $color->codigo_hex ?? '#ccc' }};
                                                    box-shadow: 0 0 3px rgba(0,0,0,0.3);
                                                ">
                                            </div>
                                        @endforeach

                                    </div>
                                @else
                                    <span class="text-muted">Sin colores</span>
                                @endif
                            </td>
                            

                            <td>
                                <a href="{{ route('productos.edit', $producto->id) }}"
                                class="btn btn-sm btn-warning mb-2 w-100">
                                    Editar
                                </a>

                                @if(auth()->user()->role == 'admin')
                                    <form action="{{ route('productos.destroy', $producto->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger w-100">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection
