@extends('app')

@section('content')

<div class="container mt-1">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color:#01746D;">
                    <h4 class="mb-0 text-white">Agregar Producto</h4>
                </div>

                <div class="card-body">

                    {{-- MENSAJE SUCCESS --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- MENSAJES DE ERROR --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- NOMBRE --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                placeholder="Ingrese el nombre del producto"
                                required>
                        </div>

                        {{-- DESCRIPCIÓN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="description"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Descripción del producto">{{ old('description') }}</textarea>
                        </div>

                        {{-- CATEGORÍA --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('category_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PRECIO --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Precio</label>
                            <input type="number"
                                step="0.01"
                                name="price"
                                class="form-control"
                                value="{{ old('price') }}"
                                placeholder="0.00"
                                required>
                        </div>

                        {{-- STOCK --}}
                        <div class="mb-3">
                            <label class="form-label">Cantidad (Stock)</label>
                            <input type="number"
                                name="quantity"
                                class="form-control"
                                min="0"
                                value="{{ old('quantity', 0) }}"
                                required>
                        </div>

                        {{-- TAMAÑOS --}}
                        <div class="mb-3">
                            <label class="form-label">Tamaños disponibles</label>
                            <input type="text"
                                name="size"
                                class="form-control"
                                value="{{ old('size') }}"
                                placeholder="Ej: S,M,L o 20oz,30oz">
                        </div>

                        {{-- COLORES EXISTENTES --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Colores disponibles</label>
                            <select name="colores[]" class="form-select" multiple>
                                @foreach($colores as $color)
                                    <option value="{{ $color->id }}"
                                        {{ collect(old('colores'))->contains($color->id) ? 'selected' : '' }}>
                                        {{ $color->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Puedes seleccionar varios colores</small>
                        </div>

                        {{-- CANTIDAD POR COLOR Y TAMAÑO --}}
                        <div class="mt-3">
                            <label class="form-label fw-bold">Cantidad por color y tamaño</label>

                            <div class="alert alert-info small">
                                Escribe los tamaños arriba separados por coma. Ejemplo: S,M,L. Después coloca cuántas piezas hay por cada color y tamaño.
                            </div>

                            <div id="color-size-stock-container"></div>
                        </div>

                        {{-- NUEVO COLOR --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Agregar nuevo color</label>
                            <input type="text"
                                name="nuevo_color"
                                class="form-control"
                                value="{{ old('nuevo_color') }}"
                                placeholder="Ej: Rojo vino">
                        </div>

                        {{-- COLOR VISUAL --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Color visual</label>
                            <input type="color"
                                name="nuevo_color_hex"
                                class="form-control form-control-color"
                                value="{{ old('nuevo_color_hex', '#000000') }}">
                            <small class="text-muted">Selecciona cómo se verá el color nuevo</small>
                        </div>

                        {{-- IMAGEN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Imagen del producto</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        {{-- BOTÓN --}}
                        <div class="d-flex justify-content-end">
                            <button type="submit"
                                    class="btn px-4 text-white"
                                    style="background-color:#01746D; border-color:#01746D;">
                                Guardar Producto
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sizeInput = document.querySelector('input[name="size"]');
    const coloresSelect = document.querySelector('select[name="colores[]"]');
    const container = document.getElementById('color-size-stock-container');

    const colores = @json($colores);

    function getSelectedColors() {
        return Array.from(coloresSelect.selectedOptions).map(option => parseInt(option.value));
    }

    function getSizes() {
        if (!sizeInput.value.trim()) {
            return [];
        }

        return sizeInput.value
            .split(',')
            .map(size => size.trim())
            .filter(size => size.length > 0);
    }

    function renderStockInputs() {
        const selectedColors = getSelectedColors();
        const sizes = getSizes();

        container.innerHTML = '';

        if (selectedColors.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">Selecciona uno o más colores para capturar cantidades.</p>';
            return;
        }

        if (sizes.length === 0) {
            selectedColors.forEach(colorId => {
                const color = colores.find(c => c.id === colorId);
                if (!color) return;

                container.innerHTML += `
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span style="
                            width:20px;
                            height:20px;
                            border-radius:50%;
                            background:${color.codigo_hex ?? '#cccccc'};
                            border:1px solid #ccc;
                            display:inline-block;
                        "></span>

                        <label class="mb-0" style="min-width:140px;">
                            ${color.nombre}
                        </label>

                        <input type="number"
                            name="cantidad_color[${color.id}]"
                            class="form-control"
                            style="max-width:120px;"
                            min="0"
                            value="0">
                    </div>
                `;
            });

            return;
        }

        selectedColors.forEach(colorId => {
            const color = colores.find(c => c.id === colorId);
            if (!color) return;

            let html = `
                <div class="border rounded p-3 mb-3">
                    <div class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <span style="
                            width:20px;
                            height:20px;
                            border-radius:50%;
                            background:${color.codigo_hex ?? '#cccccc'};
                            border:1px solid #ccc;
                            display:inline-block;
                        "></span>
                        ${color.nombre}
                    </div>

                    <div class="row g-2">
            `;

            sizes.forEach(size => {
                html += `
                    <div class="col-md-4">
                        <label class="form-label small">${size}</label>
                        <input type="number"
                            name="cantidad_color_size[${color.id}][${size}]"
                            class="form-control"
                            min="0"
                            value="0">
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;

            container.innerHTML += html;
        });
    }

    sizeInput.addEventListener('input', renderStockInputs);
    coloresSelect.addEventListener('change', renderStockInputs);

    renderStockInputs();
});

</script>

@endsection