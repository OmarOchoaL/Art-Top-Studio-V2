<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de Entrega</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 28px;
            background: #ffffff;
        }

        .sheet {
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 24px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #01746D;
            padding-bottom: 12px;
            margin-bottom: 22px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #01746D;
            margin-bottom: 4px;
        }

        .header-subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 14px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .grid td {
            vertical-align: top;
            padding: 8px 6px;
        }

        .label {
            width: 150px;
            font-weight: bold;
            color: #374151;
        }

        .value {
            color: #111827;
        }

        .references-box {
            margin-top: 14px;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
        }

        .references-title {
            font-weight: bold;
            margin-bottom: 6px;
            color: #374151;
        }

        .empty {
            text-align: center;
            padding: 24px;
            color: #6b7280;
            border: 1px dashed #d1d5db;
            border-radius: 10px;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="sheet">

        <div class="header">
            <div class="header-title">Art Top Studio</div>
            <div class="header-subtitle">Formato de datos de entrega</div>
        </div>

        @if($pedido->address)
            <div class="section-title">Información de la dirección</div>

            <table class="grid">
                <tr>
                    <td class="label">Apodo:</td>
                    <td class="value">{{ $pedido->address->alias ?? 'Sin apodo' }}</td>
                </tr>

                <tr>
                    <td class="label">Nombre:</td>
                    <td class="value">{{ $pedido->address->recipient_name ?? 'No especificado' }}</td>
                </tr>

                <tr>
                    <td class="label">Teléfono:</td>
                    <td class="value">{{ $pedido->address->phone ?? 'Sin teléfono' }}</td>
                </tr>

                <tr>
                    <td class="label">Calle:</td>
                    <td class="value">{{ $pedido->address->street }}</td>
                </tr>

                @if($pedido->address->neighborhood)
                    <tr>
                        <td class="label">Colonia:</td>
                        <td class="value">{{ $pedido->address->neighborhood }}</td>
                    </tr>
                @endif

                <tr>
                    <td class="label">Ciudad:</td>
                    <td class="value">{{ $pedido->address->city }}</td>
                </tr>

                <tr>
                    <td class="label">Estado:</td>
                    <td class="value">{{ $pedido->address->state }}</td>
                </tr>

                <tr>
                    <td class="label">Código postal:</td>
                    <td class="value">{{ $pedido->address->zip_code ?? $pedido->address->postal_code }}</td>
                </tr>
            </table>

            @if($pedido->address->references)
                <div class="references-box">
                    <div class="references-title">Referencias</div>
                    <div>{{ $pedido->address->references }}</div>
                </div>
            @endif
        @else
            <div class="empty">
                No hay dirección registrada.
            </div>
        @endif

        <div class="footer">
            Art Top Studio
        </div>

    </div>

</body>
</html>