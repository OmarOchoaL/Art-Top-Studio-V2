@extends('layouts.shop')

@section('content')

<style>
.color-option {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.color-option input {
    display: none;
}

.color-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid #ccc;
    transition: 0.2s;
}

/* COLOR SELECCIONADO */
.color-option input:checked + .color-circle {
    border: 3px solid #01746D;
    box-shadow: 0 0 0 2px rgba(1,116,109,0.2);
    transform: scale(1.1);
}
</style>

<div class="container mt-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">

        <div class="col-md-6">
            <div class="card p-3 shadow-sm">

                @if($product->image)
                    <img src="{{ asset('images/products/' . $product->image) }}"
                        class="img-fluid rounded"
                        style="max-height:500px; object-fit:cover;">
                @else
                    <img src="https://via.placeholder.com/500x400"
                        class="img-fluid rounded">
                @endif

            </div>
        </div>

        <div class="col-md-6">

            <div class="card p-4 shadow-sm h-100 d-flex flex-column">

                <h3 class="mb-3">{{ $product->name }}</h3>

                <h4 class="fw-bold mb-3" style="color:#01746D;">
                    Precio: $<span id="base-price">{{ $product->price }}</span>
                </h4>

                <p class="text-muted">
                    {{ $product->description ?? 'Sin descripción disponible' }}
                </p>

                {{-- COLORES --}}
                @if($product->colores->count())
                    <div class="mb-3">
                        <label class="fw-bold d-block mb-2">Colores disponibles:</label>
                        <label class=" d-block mb-2">Elige uno para personalizar.</label>

                        <div class="d-flex flex-wrap gap-3 mt-2">
                            @foreach($product->colores as $color)
                                <label class="color-option">
                                    <input type="radio"
                                        name="color_preview"
                                        value="{{ $color->id }}"
                                        data-nombre="{{ $color->nombre }}"
                                        data-hex="{{ $color->codigo_hex ?? '#cccccc' }}"
                                        data-stock="{{ $color->pivot->cantidad ?? 0 }}"
                                        {{ ($color->pivot->cantidad ?? 0) <= 0 ? 'disabled' : '' }}>

                                    <div class="color-circle"
                                        style="
                                            background-color: {{ $color->codigo_hex ?? '#cccccc' }};
                                            opacity: {{ ($color->pivot->cantidad ?? 0) <= 0 ? '0.4' : '1' }};
                                        ">
                                    </div>

                                    <small>
                                        {{ $color->nombre }}<br>

                                        @if(($color->pivot->cantidad ?? 0) > 0)
                                            <span class="text-muted">{{ $color->pivot->cantidad }} disponibles</span>
                                        @else
                                            <span class="text-danger fw-bold">Agotado</span>
                                        @endif
                                    </small>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- TAMAÑOS --}}
                <div class="mb-3">
                    <label class="fw-bold">Tamaños disponibles:</label>
                    <div id="tamanosDisponibles" class="mt-2 text-muted">
                        Selecciona un color para ver los tamaños disponibles.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Cantidad disponible:</label>
                    <div class="mt-2">
                        <span id="cantidadDisponibleGeneral" class="text-muted">
                            Selecciona un color y tamaño para ver la disponibilidad.
                        </span>
                    </div>
                </div>

                @guest
                    <div class="alert alert-warning mt-3">
                        <strong>🔒 Inicia sesión o crea una cuenta</strong> para personalizar este producto, agregarlo al carrito o continuar con tu compra.
                    </div>
                @endguest

                <div class="mt-3">

                    @auth
                        <div class="accordion" id="accordionPersonalizacion">
                            <div class="accordion-item border rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapsePersonalizacion">
                                        Personalizar producto
                                    </button>
                                </h2>

                                <div id="collapsePersonalizacion" class="accordion-collapse collapse">
                                    <div class="accordion-body">

                                        <form action="{{ route('shop.submit', $product->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Cantidad</label>
                                                <input type="number"
                                                    id="quantityInput"
                                                    name="quantity"
                                                    class="form-control"
                                                    min="1"
                                                    value="1"
                                                    required>
                                                <small id="stockColorText" class="text-muted d-block mt-2">
                                                    Selecciona un color para ver la cantidad disponible.
                                                </small>
                                                <small id="stockErrorText" class="text-danger d-none">
                                                    Cantidad excedida para este color. Comunícate al WhatsApp 6367009579 para realizar un pedido especial.
                                                </small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tamaño</label>
                                                <select name="size" id="sizeInput" class="form-select" required disabled>
                                                    <option value="">Selecciona primero un color</option>
                                                </select>

                                                <small id="sizeStockText" class="text-muted d-block mt-2">
                                                    Selecciona color y tamaño para ver disponibilidad.
                                                </small>
                                            </div>

                                            <div id="size-extra-msg" style="color:#ff3b3b; font-weight:bold;"></div>
                                    
                                            {{-- COLOR SELECCIONADO --}}
                                            @if($product->colores->count())
                                                <input type="hidden" name="color" id="selectedColorInput" required>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Color seleccionado</label>
                                                    <div id="selectedColorText" class="d-flex align-items-center gap-2 text-muted">
                                                        Selecciona un color arriba.
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Descripción</label>
                                                <textarea name="description" class="form-control"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Imágenes</label>
                                                <input type="file" name="images[]" class="form-control" multiple>
                                            </div>

                                            <div class="d-flex flex-column gap-2">
                                                <button type="submit"
                                                        name="action"
                                                        value="buy_now"
                                                        class="btn text-white"
                                                        style="background-color:#01746D;">
                                                    Continuar compra
                                                </button>

                                                <button type="submit"
                                                        name="action"
                                                        value="add_to_cart"
                                                        class="btn btn-outline-secondary">
                                                    Agregar al carrito
                                                </button>
                                            </div>
                                            <h5 class="mt-2">
                                                Total: $<span id="final-price">{{ $product->price }}</span>
                                            </h5>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card p-3 bg-light text-center">
                            <p class="text-muted mb-3">
                                🔒 Inicia sesión para personalizar este producto
                            </p>

                            <a href="{{ route('login') }}" class="btn btn-dark mb-2">
                                Iniciar sesión
                            </a>

                            <a href="{{ route('register') }}" class="btn btn-outline-dark">
                                Crear cuenta
                            </a>
                        </div>
                    @endauth

                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const colorRadios = document.querySelectorAll('input[name="color_preview"]');
    const selectedColorInput = document.getElementById('selectedColorInput');
    const selectedColorText = document.getElementById('selectedColorText');
    const quantityInput = document.getElementById('quantityInput');
    const stockErrorText = document.getElementById('stockErrorText');
    const sizeInput = document.getElementById('sizeInput');
    const sizeStockText = document.getElementById('sizeStockText');
    const tamanosDisponibles = document.getElementById('tamanosDisponibles');
    const cantidadDisponibleGeneral = document.getElementById('cantidadDisponibleGeneral');

    const stockCombinaciones = @json($product->colorSizes);

    let stockSeleccionado = 0;

    // VALIDAR CANTIDAD
    function validarCantidad() {
        const cantidad = parseInt(quantityInput.value || 0);

        if (stockSeleccionado > 0 && cantidad > stockSeleccionado) {
            stockErrorText.classList.remove('d-none');
        } else {
            stockErrorText.classList.add('d-none');
        }
    }

    // STOCK POR TAMAÑO
    function actualizarStockPorTamaño() {
        const colorId = selectedColorInput.value;
        const size = sizeInput.value;

        if (!colorId || !size) {
            stockSeleccionado = 0;
            quantityInput.removeAttribute('max');
            sizeStockText.innerText = 'Selecciona color y tamaño.';
            cantidadDisponibleGeneral.innerText = 'Selecciona color y tamaño.';
            updatePrice();
            return;
        }

        const encontrado = stockCombinaciones.find(item =>
            parseInt(item.color_id) === parseInt(colorId) &&
            item.size === size
        );

        stockSeleccionado = encontrado ? parseInt(encontrado.quantity || 0) : 0;

        quantityInput.value = 1;
        quantityInput.max = stockSeleccionado;

        sizeStockText.innerText = stockSeleccionado + ' disponibles.';
        cantidadDisponibleGeneral.innerText = stockSeleccionado + ' disponibles.';

        validarCantidad();
        updatePrice();
    }

    // SELECCIÓN DE COLOR
    colorRadios.forEach(radio => {
        radio.addEventListener('change', function () {

            const colorId = this.value;
            const nombreColor = this.dataset.nombre;
            const colorHex = this.dataset.hex;

            selectedColorInput.value = colorId;

            selectedColorText.innerHTML = `
                <span style="
                    display:inline-block;
                    width:24px;
                    height:24px;
                    border-radius:50%;
                    border:2px solid #ccc;
                    background-color:${colorHex};
                "></span>
                <span>${nombreColor}</span>
            `;

            const combinacionesColor = stockCombinaciones.filter(item =>
                parseInt(item.color_id) === parseInt(colorId) &&
                parseInt(item.quantity || 0) > 0
            );

            sizeInput.innerHTML = '<option value="">Selecciona tamaño</option>';
            sizeInput.disabled = false;
            tamanosDisponibles.innerHTML = '';

            if (combinacionesColor.length === 0) {
                sizeInput.innerHTML = '<option value="">Sin tamaños disponibles</option>';
                sizeInput.disabled = true;
                tamanosDisponibles.innerHTML = '<span class="text-danger">Agotado</span>';
                cantidadDisponibleGeneral.innerText = 'Agotado';
                stockSeleccionado = 0;
                updatePrice();
                return;
            }

            combinacionesColor.forEach(item => {
                sizeInput.innerHTML += `
                    <option value="${item.size}">
                        ${item.size} - ${item.quantity}
                    </option>
                `;

                tamanosDisponibles.innerHTML += `
                    <span class="border px-2 py-1 me-1">
                        ${item.size}
                    </span>
                `;
            });

            stockSeleccionado = 0;
            quantityInput.value = 1;
            quantityInput.removeAttribute('max');
            sizeStockText.innerText = 'Selecciona tamaño.';
            cantidadDisponibleGeneral.innerText = 'Selecciona tamaño.';
            stockErrorText.classList.add('d-none');

            updatePrice();
        });
    });

    // EVENTOS
    sizeInput.addEventListener('change', actualizarStockPorTamaño);

    quantityInput.addEventListener('input', function () {
        validarCantidad();
        updatePrice();
    });

    // PRECIOS
    const basePrice = parseFloat(document.getElementById('base-price').innerText);
    const finalPrice = document.getElementById('final-price');
    const sizeExtraMsg = document.getElementById('size-extra-msg');

    function getExtra(size) {
        size = (size || '').toUpperCase().trim();

        if (size === 'XL') return 15;
        if (size === 'XXL') return 30;
        if (size === 'XXXL') return 45;

        return 0;
    }

    function updatePrice() {
        let selectedSize = sizeInput ? sizeInput.value : '';
        let quantity = parseInt(quantityInput.value) || 1;
        let extraSize = getExtra(selectedSize);

        let total = (basePrice + extraSize) * quantity;

        finalPrice.innerText = total.toFixed(2);

        if (extraSize > 0) {
            sizeExtraMsg.innerText = `+$${extraSize} por talla ${selectedSize}`;
        } else {
            sizeExtraMsg.innerText = '';
        }
    }

    // INICIAL
    updatePrice();

});
</script>

@endsection