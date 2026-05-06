@extends('layouts.shop')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h3>Editar dirección</h3>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('shop.addresses.update', $address->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apodo</label>
                        <input type="text" name="alias" class="form-control" value="{{ old('alias', $address->alias) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre de quien recibe</label>
                        <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name', $address->recipient_name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $address->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Código postal</label>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $address->zip_code) }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Calle y número</label>
                        <input type="text" name="street" class="form-control" value="{{ old('street', $address->street) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Colonia</label>
                        <input type="text" name="neighborhood" class="form-control" value="{{ old('neighborhood', $address->neighborhood) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $address->city) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $address->state) }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Referencias</label>
                        <textarea name="references" class="form-control" rows="3">{{ old('references', $address->references) }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn text-white" style="background-color:#01746D; border-color:#01746D;">Actualizar dirección</button>
                    <a href="{{ route('shop.addresses.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection