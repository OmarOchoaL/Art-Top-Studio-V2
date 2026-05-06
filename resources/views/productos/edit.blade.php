@extends('app')

@section('content')

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-4">Editar Producto</h3>

            <form action="{{ route('productos.update', $producto->id) }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="name"
                        class="form-control"
                        value="{{ $producto->name }}" required>
                </div>

                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="description"
                        class="form-control">{{ $producto->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Precio</label>
                    <input type="number" step="0.01"
                        name="price"
                        class="form-control"
                        value="{{ $producto->price }}" required>
                </div>

                <div class="mb-3">
                    <label>Cantidad (Stock)</label>
                    <input type="number"
                        name="quantity"
                        class="form-control"
                        value="{{ $producto->quantity }}"
                        min="0"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tamaños disponibles</label>
                    <input type="text"
                        name="size"
                        class="form-control"
                        value="{{ $producto->size }}"
                        placeholder="Ej: S,M,L o 20oz,30oz">
                </div>

                <div class="mb-3">
                    <label>Categoría</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ $producto->category_id == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Imagen</label>
                    <input type="file" name="image" class="form-control">
                </div>

                @if($producto->image)
                    <div class="mb-3">
                        <img src="{{ asset('images/products/'.$producto->image) }}" width="120">
                    </div>
                @endif

                <div class="mb-3">
                <label class="form-label fw-bold">Colores disponibles</label>

                @foreach($colores as $color)
                    @php
                        $colorProducto = $producto->colores->firstWhere('id', $color->id);
                    @endphp

                    <div class="form-check d-flex align-items-center gap-2 mb-2">
                        <input
                            type="checkbox"
                            name="colores[]"
                            value="{{ $color->id }}"
                            class="form-check-input color-check"
                            id="color_{{ $color->id }}"
                            {{ $colorProducto ? 'checked' : '' }}
                        >

                        <span style="
                            display:inline-block;
                            width:20px;
                            height:20px;
                            border-radius:50%;
                            border:1px solid #ccc;
                            background-color: {{ $color->codigo_hex ?? '#cccccc' }};
                        "></span>

                        <label class="form-check-label mb-0" for="color_{{ $color->id }}">
                            {{ $color->nombre }}
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Cantidad por color y tamaño</label>

                <div class="alert alert-info small">
                    Escribe los tamaños arriba separados por coma. Ejemplo: S,M,L. Después coloca cuántas piezas hay por cada color y tamaño.
                </div>

                <div id="color-size-stock-container"></div>
            </div>

                <div class="mb-3">
                    <label>Agregar nuevo color</label>
                    <input type="text" name="nuevo_color" class="form-control" placeholder="Ej: Rojo vino">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Color visual</label>
                    <input type="color"
                        name="nuevo_color_hex"
                        class="form-control form-control-color"
                        value="#000000"
                        title="Selecciona un color">
                    <small class="text-muted">Selecciona cómo se verá el color nuevo.</small>
                </div>

                <div class="card p-4">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="submit" class="btn btn-lg text-white px-4"
                                style="background-color:#01746D; border-color:#01746D;">
                            Actualizar
                        </button>

                        <a href="{{ route('productos.index') }}"
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sizeInput = document.querySelector('input[name="size"]');
    const container = document.getElementById('color-size-stock-container');

    const colores = @json($colores);
    const existingStocks = @json($producto->colorSizes ?? []);
    const colorPivot = @json($producto->colores);

    function getSelectedColors() {
        return Array.from(document.querySelectorAll('.color-check:checked')).map(input => parseInt(input.value));
    }

    function getSizes() {
        if (!sizeInput.value.trim()) return [];

        return sizeInput.value
            .split(',')
            .map(size => size.trim())
            .filter(size => size.length > 0);
    }

    function getExistingQuantity(colorId, size) {
        const found = existingStocks.find(item =>
            parseInt(item.color_id) === parseInt(colorId) &&
            item.size === size
        );

        return found ? found.quantity : 0;
    }

    function getExistingColorQuantity(colorId) {
        const found = colorPivot.find(item => parseInt(item.id) === parseInt(colorId));

        if (found && found.pivot) {
            return found.pivot.cantidad ?? 0;
        }

        return 0;
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

                const cantidad = getExistingColorQuantity(color.id);

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
                            value="${cantidad}">
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
                const cantidad = getExistingQuantity(color.id, size);

                html += `
                    <div class="col-md-4">
                        <label class="form-label small">${size}</label>
                        <input type="number"
                            name="cantidad_color_size[${color.id}][${size}]"
                            class="form-control"
                            min="0"
                            value="${cantidad}">
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

    document.querySelectorAll('.color-check').forEach(check => {
        check.addEventListener('change', renderStockInputs);
    });

    renderStockInputs();
});


</script>

@endsection