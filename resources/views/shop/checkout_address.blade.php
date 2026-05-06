@extends('layouts.shop')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h3>Selecciona una dirección</h3>
        <p class="text-muted mb-0">Antes de continuar con la compra, elige una dirección de envío.</p>
    </div>

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

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($addresses->count() > 0)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Tus direcciones</h5>

                <form action="{{ route('shop.checkout.selectAddress') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Selecciona una dirección</label>
                        <select name="address_id" class="form-select" required>
                            <option value="">Selecciona...</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address->id }}">
                                    {{ $address->alias ?? $address->name }} - {{ $address->street }}, {{ $address->city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn text-white" style="background-color:#01746D; border-color:#01746D;">Continuar</button>
                        <a href="{{ route('shop.addresses.index') }}" class="btn btn-outline-primary">Agregar o administrar direcciones</a>
                        <a href="{{ route('shop.cart') }}" class="btn btn-outline-secondary">Volver al carrito</a>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">No tienes direcciones guardadas</h5>
                <p class="text-muted">Agrega una dirección para continuar con tu compra.</p>

                <form action="{{ route('shop.checkout.saveAddress') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apodo</label>
                            <input type="text" name="alias" class="form-control" value="{{ old('alias') }}" placeholder="Casa, Trabajo..." required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de quien recibe</label>
                            <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código postal</label>
                            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Calle y número</label>
                            <input type="text" name="street" class="form-control" value="{{ old('street') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Colonia</label>
                            <input type="text" name="neighborhood" class="form-control" value="{{ old('neighborhood') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state') }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Referencias</label>
                            <textarea name="references" class="form-control" rows="3">{{ old('references') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn text-white" style="background-color:#01746D; border-color:#01746D;">Guardar dirección y continuar</button>
                        <a href="{{ route('shop.cart') }}" class="btn btn-outline-secondary">Volver al carrito</a>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection