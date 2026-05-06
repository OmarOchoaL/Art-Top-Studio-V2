@extends('layouts.shop')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h3>Resumen de compra</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Productos</h5>

                    @php $grandTotal = 0; @endphp

                    @foreach($cart as $item)
                        @php
                            $subtotal = $item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1));
                            $grandTotal += $subtotal;
                        @endphp

                        <div class="d-flex justify-content-between border-bottom py-3">
                            <div>
                                <div class="fw-bold">{{ $item['name'] ?? 'Producto' }}</div>

                                @if(!empty($item['size']))
                                    <div class="text-muted small">Tamaño: {{ $item['size'] }}</div>
                                @endif

                                @if(!empty($item['color']))
                                    <div class="text-muted small">Color: {{ $item['color'] }}</div>
                                @endif

                                @if(!empty($item['description']))
                                    <div class="text-muted small">Descripción: {{ $item['description'] }}</div>
                                @endif

                                <div class="text-muted small">Cantidad: {{ $item['quantity'] ?? 1 }}</div>
                            </div>

                            <div class="text-end">
                                <div>${{ number_format($item['price'] ?? 0, 2) }}</div>
                                <div class="fw-bold">${{ number_format($subtotal, 2) }}</div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between mt-3">
                        <strong>Total</strong>
                        <strong>${{ number_format($total ?? $grandTotal, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Dirección seleccionada</h5>

                    <div class="mb-2"><strong>{{ $selectedAddress->alias ?? $selectedAddress->name }}</strong></div>
                    <div>{{ $selectedAddress->recipient_name ?? '' }}</div>
                    <div>{{ $selectedAddress->street ?? '' }}</div>

                    @if(!empty($selectedAddress->neighborhood))
                        <div>{{ $selectedAddress->neighborhood }}</div>
                    @endif

                    <div>{{ $selectedAddress->city ?? '' }}, {{ $selectedAddress->state ?? '' }}</div>
                    <div>CP {{ $selectedAddress->zip_code ?? $selectedAddress->postal_code ?? '' }}</div>

                    @if(!empty($selectedAddress->phone))
                        <div>Tel: {{ $selectedAddress->phone }}</div>
                    @endif

                    @if(!empty($selectedAddress->references))
                        <div class="text-muted mt-2">Referencias: {{ $selectedAddress->references }}</div>
                    @endif

                    <div class="mt-4">
                        <div class="alert alert-info small mb-3">
                            <strong>Nota:</strong> Si requieres factura, manda un mensaje por WhatsApp al número oficial de la tienda:
                            <strong>6367009579</strong>.
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <a href="{{ route('shop.checkout') }}" class="btn btn-outline-secondary">Cambiar dirección</a>

                            <form action="{{ route('shop.checkout.payment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="origin" value="online">
                            <input type="hidden" name="shipping_address_id" value="{{ $selectedAddress->id }}">
                            <input type="hidden" name="payment_method" value="paypal">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha de entrega</label>
                                <input type="date"
                                    id="fecha_entrega"
                                    name="fecha_entrega"
                                    class="form-control"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                    <small class="text-muted d-block mt-2">
                                        La elaboración del pedido requiere mínimo 3 a 5 días de operación. Este tiempo no incluye traslado ni tiempo de envío. Los domingos no se trabaja.
                                    </small>
                            </div>

                            <button type="submit"
                                    id="btnConfirmar"
                                    class="btn text-white w-100"
                                    style="background-color:#01746D; border-color:#01746D;"
                                    disabled>
                                Confirmar compra
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const fechaInput = document.getElementById('fecha_entrega');
    const btn = document.getElementById('btnConfirmar');

    fechaInput.addEventListener('change', function () {
        if (this.value) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    });
</script>
@endsection