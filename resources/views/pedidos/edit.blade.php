@extends('app')

@section('content')

<div class="container mt-3">

<h3 class="mb-3">Editar Pedido</h3>

@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="{{ route('pedidos.update',$pedido->id) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="mb-3">
    <label class="form-label">Nombre del cliente</label>
    <input type="text" name="customer_name" class="form-control" value="{{ $pedido->customer_name }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Número de teléfono</label>
    <input type="text" name="customer_phone" class="form-control" value="{{ $pedido->customer_phone }}" required>
</div>

<div class="mb-3">
<label class="form-label fw-bold">Origen del pedido</label>
<select name="origin" class="form-select" required>
<option value="">Selecciona</option>
<option value="local" {{ $pedido->origin=='local'?'selected':'' }}>Local</option>
<option value="online" {{ $pedido->origin=='online'?'selected':'' }}>En línea</option>
</select>
</div>

<hr>

<h5>Productos</h5>

<div id="items-container"></div>

<button type="button" id="add-item" class="btn btn-outline-primary mb-3">
+ Agregar producto
</button>

<hr>

<div class="text-end mb-3">
<h4>
Total del pedido:
<strong id="pedido-total">$0.00</strong>
</h4>
</div>

<div class="row mt-3">
<div class="col-md-4">
<label class="form-label">Descuento</label>
<select id="discount" name="discount" class="form-select">
    <option value="0" {{ $pedido->discount == 0 ? 'selected' : '' }}>Sin descuento</option>
    <option value="10" {{ $pedido->discount == 10 ? 'selected' : '' }}>10%</option>

    @if(auth()->user()->role === 'admin')
        <option value="15" {{ $pedido->discount == 15 ? 'selected' : '' }}>15%</option>
        <option value="20" {{ $pedido->discount == 20 ? 'selected' : '' }}>20%</option>
    @endif
</select>
</div>
</div>

<div class="row mt-3">
<div class="col-md-4">
<label class="form-label">Fecha de entrega</label>
<input type="date"
name="fecha_entrega"
class="form-control"
value="{{ $pedido->fecha_entrega ? date('Y-m-d', strtotime($pedido->fecha_entrega)) : '' }}">
</div>

<div class="col-md-4">
<label class="form-label">Anticipo</label>
<input type="number"
name="anticipo"
class="form-control"
step="0.01"
value="{{ $pedido->anticipo ?? 0 }}"
id="anticipo-input">
</div>

<div class="col-md-4">
<label class="form-label">Método de pago</label>
<select name="payment_method" class="form-select">
<option value="">Seleccionar</option>
<option value="efectivo" {{ $pedido->payment_method=='efectivo'?'selected':'' }}>💵 Efectivo</option>
<option value="tarjeta" {{ $pedido->payment_method=='tarjeta'?'selected':'' }}>💳 Tarjeta</option>
</select>
</div>

<div class="col-md-4 mt-3">
<label class="form-label">Restante</label>
<input type="text" class="form-control" id="restante" readonly>
</div>
</div>

<hr>

<div class="mb-3">
    <label class="form-label">Imagen del pedido</label>
    <input type="file" name="images[]" class="form-control" multiple>
</div>

<button type="submit" class="btn text-white" style="background:#01746D;">
Actualizar Pedido
</button>

</form>

</div>

<template id="item-template">
<div class="card mb-3 item-row">
<div class="card-body">
<div class="row g-2 align-items-end">

<div class="col-md-3">
<label class="form-label">Producto</label>
<select class="form-select product-select" required>
<option value="">Selecciona</option>
@foreach($productos as $producto)
<option value="{{ $producto->id }}" data-price="{{ $producto->price }}">
{{ $producto->name }} (${{ number_format($producto->price,2) }})
</option>
@endforeach
</select>
</div>

<div class="col-md-1">
<label class="form-label">Cantidad</label>
<input type="number" class="form-control quantity-input" min="1" value="1" required>
</div>

<div class="col-md-2">
<label class="form-label">Tamaño</label>
<select class="form-select size-input">
<option value="">Seleccionar</option>
</select>
</div>

<div class="col-md-2">
    <label class="form-label">Color</label>

    <input type="hidden" class="color-input">

    <div class="color-options d-flex flex-wrap gap-2 mt-1"></div>

    <small class="text-muted color-selected-text d-block mt-1"></small>
</div>

<div class="col-md-2">
<label class="form-label">Descripción</label>
<input type="text" class="form-control description-input" placeholder="Ej: Grabado Juan">
</div>

<div class="col-md-1 text-end">
<button type="button" class="btn btn-danger btn-sm remove-item">✕</button>
</div>

</div>
</div>
</div>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('items-container');
    const addBtn = document.getElementById('add-item');
    const template = document.getElementById('item-template').content;
    let index = 0;

    function calcularTotal() {
        let subtotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const selected = productSelect.options[productSelect.selectedIndex];

            if (!selected || !selected.value) return;

            const price = parseFloat(selected.dataset.price || 0);
            const qty = parseInt(quantityInput.value || 0);

            subtotal += price * qty;
        });

        const descuento = parseFloat(document.getElementById('discount').value || 0);
        const total = subtotal - (subtotal * descuento / 100);

        document.getElementById('pedido-total').innerText = '$' + total.toFixed(2);
        calcularRestante();
    }

    function calcularRestante() {
        const totalText = document.getElementById('pedido-total').innerText.replace('$', '');
        const total = parseFloat(totalText) || 0;
        const anticipo = parseFloat(document.getElementById('anticipo-input').value) || 0;
        const restante = total - anticipo;

        document.getElementById('restante').value = '$' + restante.toFixed(2);
    }

    function addItem(item = null){
        const clone = document.importNode(template, true);
        const row = clone.querySelector('.item-row');

        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const sizeInput = row.querySelector('.size-input');
        const colorInput = row.querySelector('.color-input');
        const colorOptions = row.querySelector('.color-options');
        const colorSelectedText = row.querySelector('.color-selected-text');
        const descriptionInput = row.querySelector('.description-input');

        productSelect.name = `items[${index}][product_id]`;
        quantityInput.name = `items[${index}][quantity]`;
        sizeInput.name = `items[${index}][size]`;
        colorInput.name = `items[${index}][color]`;
        descriptionInput.name = `items[${index}][description]`;

        if(item){
            productSelect.value = item.product_id;
            quantityInput.value = item.quantity;
            colorInput.value = item.color ?? '';
            descriptionInput.value = item.description ?? '';

            fetch(`/api/product/${item.product_id}`)
            .then(res => res.json())
            .then(product => {
                sizeInput.innerHTML = '<option value="">Seleccionar</option>';
                colorOptions.innerHTML = '';
                colorSelectedText.innerText = item.color ? 'Seleccionado: ' + item.color : '';

                if(product.size){
                    const sizes = product.size.split(',');

                    sizes.forEach(size => {
                        const option = document.createElement('option');
                        option.value = size.trim();
                        option.innerText = size.trim();

                        if(size.trim() === item.size){
                            option.selected = true;
                        }

                        sizeInput.appendChild(option);
                    });
                }

                if(product.colores && product.colores.length){
                    product.colores.forEach(color => {
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('text-center');

                        const circle = document.createElement('div');
                        circle.title = color.nombre;
                        circle.style.width = '26px';
                        circle.style.height = '26px';
                        circle.style.borderRadius = '50%';
                        circle.style.border = '2px solid #ccc';
                        circle.style.backgroundColor = color.codigo_hex ?? '#cccccc';
                        circle.style.cursor = 'pointer';
                        circle.style.boxShadow = '0 0 3px rgba(0,0,0,0.25)';
                        circle.classList.add('color-circle-item');

                        if(color.nombre === item.color){
                            circle.style.border = '3px solid #01746D';
                            circle.style.transform = 'scale(1.08)';
                        }

                        circle.addEventListener('click', () => {
                            colorInput.value = color.nombre;
                            colorSelectedText.innerText = 'Seleccionado: ' + color.nombre;

                            colorOptions.querySelectorAll('.color-circle-item').forEach(c => {
                                c.style.border = '2px solid #ccc';
                                c.style.transform = 'scale(1)';
                            });

                            circle.style.border = '3px solid #01746D';
                            circle.style.transform = 'scale(1.08)';
                        });

                        const label = document.createElement('small');
                        label.innerText = color.nombre;

                        wrapper.appendChild(circle);
                        wrapper.appendChild(label);
                        colorOptions.appendChild(wrapper);
                    });
                }

                calcularTotal();
            });
        }

        productSelect.addEventListener('change', async () => {
            if(!productSelect.value) return;

            const res = await fetch(`/api/product/${productSelect.value}`);
            const product = await res.json();

            sizeInput.innerHTML = '<option value="">Seleccionar</option>';
            colorOptions.innerHTML = '';
            colorInput.value = '';
            colorSelectedText.innerText = '';

            if(product.size){
                const sizes = product.size.split(',');

                sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.trim();
                    option.innerText = size.trim();
                    sizeInput.appendChild(option);
                });
            }

            if(product.colores && product.colores.length){
                product.colores.forEach(color => {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('text-center');

                    const circle = document.createElement('div');
                    circle.title = color.nombre;
                    circle.style.width = '26px';
                    circle.style.height = '26px';
                    circle.style.borderRadius = '50%';
                    circle.style.border = '2px solid #ccc';
                    circle.style.backgroundColor = color.codigo_hex ?? '#cccccc';
                    circle.style.cursor = 'pointer';
                    circle.style.boxShadow = '0 0 3px rgba(0,0,0,0.25)';
                    circle.classList.add('color-circle-item');

                    circle.addEventListener('click', () => {
                        colorInput.value = color.nombre;
                        colorSelectedText.innerText = 'Seleccionado: ' + color.nombre;

                        colorOptions.querySelectorAll('.color-circle-item').forEach(c => {
                            c.style.border = '2px solid #ccc';
                            c.style.transform = 'scale(1)';
                        });

                        circle.style.border = '3px solid #01746D';
                        circle.style.transform = 'scale(1.08)';
                    });

                    const label = document.createElement('small');
                    label.innerText = color.nombre;

                    wrapper.appendChild(circle);
                    wrapper.appendChild(label);
                    colorOptions.appendChild(wrapper);
                });
            }

            calcularTotal();
        });

        quantityInput.addEventListener('input', calcularTotal);

        row.querySelector('.remove-item').addEventListener('click', () => {
            row.remove();
            calcularTotal();
        });

        container.appendChild(row);
        index++;
        calcularTotal();
    }

    document.getElementById('anticipo-input').addEventListener('input', calcularRestante);
    document.getElementById('discount').addEventListener('change', calcularTotal);
    addBtn.addEventListener('click', () => addItem());

    const items = @json($pedido->items->values());

    items.forEach(item => addItem(item));
});
</script>

@endsection