@extends('layouts.shop')

@section('content')

<div class="container mt-4">

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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Mi carrito</h3>

        <a href="{{ route('shop.index') }}" class="btn btn-light">
            Seguir comprando
        </a>
    </div>

    @if(!empty($cart) && count($cart) > 0)
        <div class="row">
            <div class="col-lg-8">

                @foreach($cart as $item)
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">

                                <div class="col-md-3">
                                    @if(!empty($item['image']))
                                        <img src="{{ asset('images/products/' . $item['image']) }}"
                                                class="img-fluid rounded"
                                                style="max-height:120px; object-fit:cover;">
                                    @else
                                        <img src="https://via.placeholder.com/150x120"
                                                class="img-fluid rounded">
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-2">{{ $item['name'] }}</h5>

                                    <p class="mb-1">
                                        <strong>Precio:</strong>
                                        ${{ number_format($item['price'], 2) }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>Cantidad:</strong>
                                        {{ $item['quantity'] }}
                                    </p>

                                    @if(!empty($item['size']))
                                        <p class="mb-1">
                                            <strong>Tamaño:</strong>
                                            {{ $item['size'] }}
                                        </p>
                                    @endif

                                    @if(in_array(strtoupper($item['size']), ['XL','XXL','XXXL']))
                                        <div class="text-muted small">
                                            Extra por talla:
                                            @if($item['size'] == 'XL') +$15
                                            @elseif($item['size'] == 'XXL') +$30
                                            @elseif($item['size'] == 'XXXL') +$45
                                            @endif
                                        </div>
                                    @endif

                                    @if(!empty($item['color']))
                                        <p class="mb-1">
                                            <strong>Color:</strong>
                                            {{ $item['color'] }}
                                        </p>
                                    @endif

                                    @if(!empty($item['extras']))
                                        <div class="text-muted small">
                                            Extras:
                                            @foreach($item['extras'] as $extraId)
                                                @php
                                                    $extra = \App\Models\ProductExtra::find($extraId);
                                                @endphp
                                                {{ $extra->name ?? '' }},
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!empty($item['description']))
                                        <p class="mb-1">
                                            <strong>Descripción:</strong>
                                            {{ $item['description'] }}
                                        </p>
                                    @endif

                                    @if(!empty($item['customer_name']))
                                        <p class="mb-1">
                                            <strong>Cliente:</strong>
                                            {{ $item['customer_name'] }}
                                        </p>
                                    @endif

                                    @if(!empty($item['customer_phone']))
                                        <p class="mb-1">
                                            <strong>Teléfono:</strong>
                                            {{ $item['customer_phone'] }}
                                        </p>
                                    @endif

                                    @if(!empty($item['reference_images']) && is_array($item['reference_images']) && count($item['reference_images']) > 0)
                                        <div class="mt-2">
                                            <strong>Imágenes de referencia:</strong>
                                            @foreach($item['reference_images'] as $img)
                                                <div class="small">
                                                    <a href="{{ asset('images/shop_orders/' . $img) }}" target="_blank">
                                                        {{ $img }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <h5 style="color:#01746D;">
                                        ${{ number_format($item['subtotal'], 2) }}
                                    </h5>

                                    @if(!empty($db_cart) && $db_cart)
                                        <form action="{{ route('shop.cart.remove', $item['id']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm mt-2">
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('shop.cart.remove', $loop->index) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm mt-2">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3">Resumen</h4>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Total</span>
                            <strong>${{ number_format($total, 2) }}</strong>
                        </div>

                        <!-- <div class="alert alert-info small mb-3">
                            <strong>Nota:</strong> Si requieres factura, manda un mensaje por WhatsApp al número oficial de la tienda:
                            <strong>6367009579</strong>.
                        </div> -->

                        <hr>

                        @auth
                            @if(auth()->user()->role === 'cliente')
                                <a href="{{ route('shop.checkout') }}" class="btn text-white" style="background-color:#01746D; border-color:#01746D;">
                                Continuar con la compra
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="btn w-100 text-white mb-2"
                                style="background-color:#01746D; border-color:#01746D;">
                                Iniciar sesión para comprar
                            </a>
                        @endauth

                        <form action="{{ route('shop.cart.clear') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                Vaciar carrito
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <h4 class="mb-3">Tu carrito está vacío</h4>
                <p class="text-muted">Agrega productos para continuar con tu compra.</p>

                <a href="{{ route('shop.index') }}"
                        class="btn text-white"
                        style="background-color:#01746D; border-color:#01746D;">
                    Ir al catálogo
                </a>
            </div>
        </div>
    @endif

</div>

@endsection