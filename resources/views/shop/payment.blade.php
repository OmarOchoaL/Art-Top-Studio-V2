@extends('layouts.shop')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Método de pago</h3>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Resumen</h5>

                    @foreach($cart->items as $item)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <strong>{{ $item->product->name ?? 'Producto' }}</strong><br>
                                <small>Cantidad: {{ $item->quantity }}</small>

                                @if($item->size)
                                    <small> | Tamaño: {{ $item->size }}</small>
                                @endif
                            </div>

                            <strong>${{ number_format($item->subtotal, 2) }}</strong>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between mt-3">
                        <strong>Total</strong>
                        <strong>${{ number_format($total, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Pagar con PayPal</h5>

                    <p class="text-muted small">
                        Usa PayPal Sandbox para hacer pruebas sin dinero real.
                    </p>

                    <div id="paypal-button-container"></div>

                    <form id="confirmarPedidoForm"
                        action="{{ route('shop.checkout.process') }}"
                        method="POST"
                        class="d-none">
                        @csrf

                        <input type="hidden" name="origin" value="online">
                        <input type="hidden" name="shipping_address_id" value="{{ session('checkout_payment.shipping_address_id') }}">
                        <input type="hidden" name="payment_method" value="paypal">
                        <input type="hidden" name="fecha_entrega" value="{{ session('checkout_payment.fecha_entrega') }}">
                        <input type="hidden" name="paypal_order_id" id="paypal_order_id">
                    </form>

                    <a href="{{ route('shop.checkout.summary') }}"
                        class="btn btn-outline-secondary w-100 mt-3">
                        Volver al resumen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=MXN"></script>

<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '{{ number_format($total, 2, '.', '') }}'
                }
            }]
        });
    },

    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            document.getElementById('paypal_order_id').value = data.orderID;
            document.getElementById('confirmarPedidoForm').submit();
        });
    },

    onError: function(err) {
        alert('Ocurrió un error con PayPal. Intenta nuevamente.');
        console.error(err);
    }
}).render('#paypal-button-container');
</script>
@endsection