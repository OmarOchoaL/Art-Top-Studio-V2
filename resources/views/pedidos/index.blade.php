@extends('app')

@section('content')

<div class="container mt-1">

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Pedidos</h3>

    <a href="{{ route('pedidos.create') }}"
    class="btn text-white"
    style="background-color:#01746D;">
        Nuevo Pedido
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if($pedidos->count() === 0)
            <p class="text-muted mb-0">No hay pedidos registrados.</p>
        @else
            <table class="table table-bordered align-middle">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Origen</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th style="min-width:350px;">Productos / Imágenes</th>
                        <th>Fecha</th>
                        <th>Entrega</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($pedidos as $pedido)
                    <tr>

                        <td>#{{ $pedido->id }}</td>

                        <td>
                            <strong>{{ $pedido->customer_name }}</strong><br>

                            @if($pedido->customer_phone)
                                <span>Tel: {{ $pedido->customer_phone }}</span><br>
                            @endif
                            @if($pedido->origin === 'online' && $pedido->address)
                                <span>
                                    <strong>Entrega:</strong>
                                    <a href="{{ route('pedidos.pdfEntrega', $pedido->id) }}"
                                    target="_blank"
                                    style="color:#01746D; font-weight:600; text-decoration:none;">
                                        {{ $pedido->address->alias ?? $pedido->address->name }}
                                    </a>
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="badge
                                {{ $pedido->origin === 'local' ? 'bg-primary' : 'bg-success' }}">
                                {{ strtoupper($pedido->origin) }}
                            </span>
                        </td>

                        <td>
                            <form action="{{ route('pedidos.estado',$pedido->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select name="status"
                                class="form-select form-select-sm
                                @if($pedido->status == 'pendiente') bg-warning text-dark
                                @elseif($pedido->status == 'urgente') bg-danger text-white
                                @elseif($pedido->status == 'entregado') bg-success text-white
                                @endif"
                                onchange="this.form.submit()">

                                    <option value="pendiente" {{ $pedido->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="urgente" {{ $pedido->status == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                    <option value="entregado" {{ $pedido->status == 'entregado' ? 'selected' : '' }}>Entregado</option>

                                </select>
                            </form>
                        </td>

                        <td>
                            <strong>Total:</strong>
                            ${{ number_format($pedido->total,2) }}

                            @if($pedido->origin === 'online')
                                <br>

                                @if($pedido->payment_method === 'paypal')
                                    <span class="text-success fw-bold">Pagado en línea</span>

                                    @if($pedido->paypal_order_id)
                                        <br>
                                        <small class="text-muted">
                                            ID PayPal: {{ $pedido->paypal_order_id }}
                                        </small>
                                    @endif

                                @else
                                    <span class="text-warning fw-bold">Pago pendiente</span>
                                @endif
                            @endif

                            <br>

                            <strong>Descuento:</strong>

                            <strong>Descuento:</strong>
                            {{ number_format($pedido->discount ?? 0, 0) }}%

                            <br>

                            <strong>Anticipo:</strong>
                            ${{ number_format($pedido->anticipo ?? 0,2) }}

                            <br>

                            <strong>Restante:</strong>
                            ${{ number_format($pedido->total - ($pedido->anticipo ?? 0),2) }}

                            @if(strtolower(trim($pedido->payment_method)) == 'efectivo')
                                <br>💵 Efectivo
                            @elseif(strtolower(trim($pedido->payment_method)) == 'tarjeta')
                                <br>💳 Tarjeta
                            @endif
                        </td>

                        <td>

                            @foreach($pedido->items as $item)

                                <div class="border rounded p-2 mb-2">

                                    <strong>{{ $item->product->name }}</strong>
                                    <br>
                                    Cantidad: {{ $item->quantity }}

                                    @if($item->size)
                                        <br>Tamaño: {{ $item->size }}
                                    @endif

                                    @if($item->color)
                                        <br>Color: {{ $item->color }}
                                    @endif

                                    @if($item->description)
                                        <br>Descripción: {{ $item->description }}
                                    @endif

                                </div>

                            @endforeach

                            @if($pedido->image && count($pedido->image))
                                <div class="mt-2">
                                    <strong>Imágenes:</strong>

                                    @foreach($pedido->image as $img)
                                        <div class="mt-2">
                                            <div class="small">
                                                @php
                                                    $imageFolder = $pedido->origin === 'online' ? 'images/shop_orders/' : 'images/orders/';
                                                @endphp
                                                <a href="{{ asset($imageFolder . $img) }}" download="{{ $img }}">
                                                    {{ $img }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-muted small mt-2">
                                    Sin imágenes
                                </div>
                            @endif

                        </td>

                        <td>
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            {{ $pedido->fecha_entrega
                                ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y')
                                : 'Sin fecha' }}
                        </td>

                        <td>

                            <a href="{{ route('pedidos.edit', $pedido->id) }}"
                                class="btn btn-sm btn-warning w-100 mb-2">
                                Editar
                            </a>

                            @if(auth()->user()->role == 'admin')

                            <button type="button"
                                class="btn btn-sm btn-danger w-100 mb-2"
                                onclick="abrirConfirmacionPedido(
                                    '{{ route('pedidos.destroy', $pedido->id) }}',
                                    'Pedido #{{ $pedido->id }} - {{ $pedido->customer_name ?? 'Sin nombre' }}'
                                )">
                                Eliminar
                            </button>

                            @endif

                            <a href="{{ route('pedidos.pdf',$pedido->id) }}"
                                class="btn btn-sm btn-dark w-100"
                                target="_blank">
                                PDF
                            </a>

                        </td>

                    </tr>
                    @endforeach

                </tbody>

            </table>
        @endif

    </div>
</div>

</div>

{{-- VENTANA SIMPLE --}}
<div id="confirmacionPedidoOverlay"
    style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,0.08);
        z-index:9999;
        align-items:center;
        justify-content:center;">

    <div style="
        width:100%;
        max-width:420px;
        background:#fff;
        border-radius:12px;
        box-shadow:0 10px 30px rgba(0,0,0,0.18);
        padding:24px;">

        <h5 class="mb-3">Confirmar eliminación</h5>

        <p id="confirmacionPedidoTexto" class="mb-3 text-muted"></p>

        <form id="confirmacionPedidoForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="mb-3">
                <label for="admin_password_pedido" class="form-label fw-bold">
                    Contraseña de administrador
                </label>
                <input type="password"
                        name="admin_password"
                        id="admin_password_pedido"
                        class="form-control"
                        autocomplete="off"
                        required>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button"
                        class="btn btn-secondary"
                        onclick="cerrarConfirmacionPedido()">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn btn-danger">
                    Eliminar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirConfirmacionPedido(actionUrl, pedidoTexto) {
        const overlay = document.getElementById('confirmacionPedidoOverlay');
        const form = document.getElementById('confirmacionPedidoForm');
        const texto = document.getElementById('confirmacionPedidoTexto');
        const password = document.getElementById('admin_password_pedido');

        form.action = actionUrl;
        texto.innerHTML = `Vas a eliminar <strong>${pedidoTexto}</strong>. Escribe tu contraseña para continuar.`;
        password.value = '';

        overlay.style.display = 'flex';

        setTimeout(() => {
            password.focus();
        }, 50);
    }

    function cerrarConfirmacionPedido() {
        const overlay = document.getElementById('confirmacionPedidoOverlay');
        const password = document.getElementById('admin_password_pedido');

        overlay.style.display = 'none';
        password.value = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.getElementById('confirmacionPedidoOverlay');

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                cerrarConfirmacionPedido();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                cerrarConfirmacionPedido();
            }
        });
    });
</script>

@endsection