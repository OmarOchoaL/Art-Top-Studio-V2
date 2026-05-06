@extends('layouts.shop')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('shop.index') }}" class="mb-4">
    <div class="row g-2 align-items-end">

        <div class="col-md-4">
            <label class="form-label fw-semibold">Buscar</label>
            <input type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Ej. taza, termo, playera...">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Categoría</label>
            <select name="category" class="form-select">
                <option value="">Todas</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Ordenar</label>
            <select name="sort" class="form-select">
                <option value="">Más recientes</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                    Precio: menor a mayor
                </option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                    Precio: mayor a menor
                </option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                    Nombre: A-Z
                </option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                    Nombre: Z-A
                </option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit"
                    class="btn text-white w-100"
                    style="background-color:#01746D; border-color:#01746D;">
                Filtrar
            </button>

            <a href="{{ route('shop.index') }}"
                class="btn btn-light w-100">
                Limpiar
            </a>
        </div>

    </div>
</form>

<div class="row">

    @forelse($products as $product)
    <div class="col-md-3 mb-4">

        <div class="card product-card h-100 shadow-sm">

            {{-- IMAGEN --}}
            @if($product->image)
                <img src="{{ asset('images/products/' . $product->image) }}"
                        class="card-img-top product-img"
                        style="height:220px; object-fit:cover;">
            @else
                <img src="https://via.placeholder.com/300x200"
                        class="card-img-top product-img"
                        style="height:220px; object-fit:cover;">
            @endif

            <div class="card-body d-flex flex-column">

                {{-- NOMBRE --}}
                <h6 class="mb-2">{{ $product->name }}</h6>

                {{-- CATEGORÍA --}}
                @if($product->category)
                    <small class="text-muted mb-2">{{ $product->category->name }}</small>
                @endif

                {{-- PRECIO --}}
                <p class="fw-bold mb-2" style="color:#01746D;">
                    ${{ number_format($product->price, 2) }}
                </p>

                {{-- COLORES DISPONIBLES --}}
                @if($product->colores->count())
                    <div class="mb-3">
                        <small class="fw-bold d-block mb-2">Colores disponibles:</small>

                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->colores as $color)
                                <div
                                    title="{{ $color->nombre }}"
                                    style="
                                        width:20px;
                                        height:20px;
                                        border-radius:50%;
                                        border:1px solid #ccc;
                                        background-color: {{ $color->codigo_hex ?? '#cccccc' }};
                                        box-shadow: 0 0 3px rgba(0,0,0,0.25);
                                    ">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mb-3">
                        <small class="text-muted">Sin colores disponibles</small>
                    </div>
                @endif

                {{-- BOTÓN --}}
                <a href="{{ route('shop.show', $product->id) }}"
                    class="btn btn-sm text-white mt-auto"
                    style="background-color:#01746D; border-color:#01746D;">
                    Ver producto
                </a>

            </div>

        </div>

    </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light border">
                No se encontraron productos con esos filtros.
            </div>
        </div>
    @endforelse

</div>

@endsection