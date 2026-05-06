@extends('layouts.shop')

@section('content')
<div class="container py-4">
    <style>
        .addresses-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 22px 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .page-subtitle {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .panel-card {
            background: #fff;
            border: none;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            height: 100%;
        }

        .panel-card .card-body {
            padding: 28px;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 18px;
        }

        .form-label-custom {
            font-size: 0.92rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-control-custom {
            height: 48px;
            border-radius: 14px;
            border: 1px solid #d1d5db;
            padding: 0 14px;
            box-shadow: none !important;
        }

        .form-control-custom:focus {
            border-color: #01746D;
            box-shadow: 0 0 0 0.2rem rgba(1, 116, 109, 0.12) !important;
        }

        .textarea-custom {
            border-radius: 14px;
            border: 1px solid #d1d5db;
            padding: 12px 14px;
            box-shadow: none !important;
            resize: none;
        }

        .textarea-custom:focus {
            border-color: #01746D;
            box-shadow: 0 0 0 0.2rem rgba(1, 116, 109, 0.12) !important;
        }

        .btn-brand {
            background-color: #01746D;
            border-color: #01746D;
            color: #fff;
            border-radius: 14px;
            height: 48px;
            font-weight: 600;
        }

        .btn-brand:hover {
            background-color: #015d58;
            border-color: #015d58;
            color: #fff;
        }

        .btn-soft {
            border-radius: 12px;
            font-weight: 600;
        }

        .address-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .saved-address {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 16px;
            background: #fafafa;
            transition: all 0.2s ease;
        }

        .saved-address:hover {
            border-color: rgba(1, 116, 109, 0.25);
            box-shadow: 0 8px 18px rgba(1, 116, 109, 0.08);
        }

        .saved-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 10px;
        }

        .saved-alias {
            font-size: 1rem;
            font-weight: 700;
            color: #01746D;
            margin-bottom: 2px;
        }

        .saved-name {
            font-size: 0.94rem;
            font-weight: 600;
            color: #111827;
        }

        .saved-info {
            color: #4b5563;
            font-size: 0.93rem;
            line-height: 1.5;
        }

        .saved-ref {
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 0.88rem;
        }

        .empty-box {
            border: 1px dashed #d1d5db;
            border-radius: 18px;
            background: #fafafa;
            padding: 28px 20px;
            text-align: center;
            color: #6b7280;
        }

        @media (max-width: 991px) {
            .panel-card .card-body,
            .page-header-box {
                padding: 20px;
            }
        }
    </style>

    <div class="addresses-wrapper">
        <div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="page-title">Mis direcciones</h3>
                <p class="page-subtitle">Agrega, edita o elimina tus direcciones de envío.</p>
            </div>

            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                Volver a la tienda
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card panel-card">
                    <div class="card-body">
                        <h5 class="section-title">Agregar dirección</h5>

                        <form action="{{ route('shop.addresses.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-label-custom">Apodo</label>
                                    <input type="text" name="alias" class="form-control form-control-custom" value="{{ old('alias') }}" placeholder="Casa, Trabajo..." required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-label-custom">Nombre de quien recibe</label>
                                    <input type="text" name="recipient_name" class="form-control form-control-custom" value="{{ old('recipient_name') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-label-custom">Teléfono</label>
                                    <input type="text" name="phone" class="form-control form-control-custom" value="{{ old('phone') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-label-custom">Código postal</label>
                                    <input type="text" name="zip_code" class="form-control form-control-custom" value="{{ old('zip_code') }}" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label form-label-custom">Calle y número</label>
                                    <input type="text" name="street" class="form-control form-control-custom" value="{{ old('street') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-label-custom">Colonia</label>
                                    <input type="text" name="neighborhood" class="form-control form-control-custom" value="{{ old('neighborhood') }}">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label form-label-custom">Ciudad</label>
                                    <input type="text" name="city" class="form-control form-control-custom" value="{{ old('city') }}" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label form-label-custom">Estado</label>
                                    <input type="text" name="state" class="form-control form-control-custom" value="{{ old('state') }}" required>
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label form-label-custom">Referencias</label>
                                    <textarea name="references" class="form-control textarea-custom" rows="4" placeholder="Color de la casa, portón, entre qué calles está, etc.">{{ old('references') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand w-100">
                                Guardar dirección
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card panel-card">
                    <div class="card-body">
                        <h5 class="section-title">Direcciones guardadas</h5>

                        @forelse($addresses as $address)
                            <div class="address-list">
                                <div class="saved-address">
                                    <div class="saved-top">
                                        <div>
                                            <div class="saved-alias">{{ $address->alias }}</div>
                                            <div class="saved-name">{{ $address->recipient_name }}</div>
                                        </div>

                                        <div class="d-flex gap-2 flex-shrink-0">
                                            <a href="{{ route('shop.addresses.edit', $address->id) }}" class="btn btn-sm btn-outline-primary btn-soft">
                                                Editar
                                            </a>

                                            <form action="{{ route('shop.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta dirección?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-soft">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="saved-info">
                                        <div>{{ $address->street }}</div>

                                        @if($address->neighborhood)
                                            <div>{{ $address->neighborhood }}</div>
                                        @endif

                                        <div>{{ $address->city }}, {{ $address->state }}, CP {{ $address->zip_code }}</div>

                                        @if($address->phone)
                                            <div>Tel: {{ $address->phone }}</div>
                                        @endif
                                    </div>

                                    @if($address->references)
                                        <div class="saved-ref">
                                            <strong>Referencias:</strong> {{ $address->references }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-box">
                                <div class="fw-semibold mb-2">No tienes direcciones guardadas todavía.</div>
                                <div>Agrega una dirección usando el formulario de la izquierda.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection