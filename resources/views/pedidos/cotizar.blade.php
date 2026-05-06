<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body{
    font-family: DejaVu Sans;
    font-size:14px;
    color:#222;
}

h2, h3, h4{
    margin-bottom: 5px;
}

p{
    margin: 4px 0;
}

table{
    width:100%;
    border-collapse: collapse;
    margin-top:10px;
}

table th, table td{
    border:1px solid #ccc;
    padding:8px;
    text-align:center;
}

th{
    background:#01746D;
    color:white;
}

.info{
    width:100%;
    margin-top:10px;
    margin-bottom:15px;
}

.info td{
    padding:6px 4px;
    vertical-align:top;
    border:none;
}

.total{
    text-align:right;
    font-size:16px;
    margin-top:20px;
    line-height:1.8;
}

.section-title{
    margin-top:15px;
    margin-bottom:8px;
    font-size:16px;
    font-weight:bold;
}

hr{
    margin:12px 0;
}
</style>
</head>
<body>

<h2>Art Top Studio</h2>

<hr>

<h3>Cotización</h3>

<table class="info">
    <tr>
        <td style="text-align:left;">
            <strong>Cliente:</strong> {{ $customer_name ?? 'No especificado' }}<br>
            <strong>Teléfono:</strong> {{ $customer_phone ?? 'Sin teléfono' }}<br>
            <strong>Fecha de elaboración:</strong> {{ date('d/m/Y') }}<br>
            <strong>Hora de elaboración:</strong> {{ date('h:i A') }}<br>
            <strong>Vendedor:</strong>
            {{ ucfirst($generado_por_rol ?? 'No especificado') }} - {{ $generado_por_nombre ?? 'No especificado' }}
        </td>
        <td style="text-align:left;">
            <strong>Fecha de entrega:</strong> {{ $fecha_entrega ?: 'No especificada' }}<br>
            <strong>Anticipo mínimo:</strong> ${{ number_format($anticipo ?? 0, 2) }}<br>
            <strong>Descuento:</strong> {{ number_format($descuento ?? 0, 0) }}%
        </td>
    </tr>
</table>

<div class="section-title">Productos</div>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Tamaño</th>
            <th>Color</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Importe</th>
        </tr>
    </thead>

    <tbody>
        @forelse($items as $item)
        <tr>
            <td>{{ $item['producto'] ?? '-' }}</td>
            <td>{{ $item['cantidad'] ?? '-' }}</td>
            <td>{{ $item['size'] ?? '-' }}</td>
            <td>{{ $item['color'] ?? '-' }}</td>
            <td>{{ $item['description'] ?? '-' }}</td>
            <td>${{ number_format($item['precio'] ?? 0, 2) }}</td>
            <td>${{ number_format($item['importe'] ?? 0, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7">No hay productos en la cotización</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="total">
    <div>Subtotal: ${{ number_format($subtotal ?? 0, 2) }}</div>
    <div>Descuento: {{ number_format($descuento ?? 0, 0) }}%</div>
    <div>Total: ${{ number_format($total ?? 0, 2) }}</div>
    <div>Anticipo mínimo: ${{ number_format($anticipo ?? 0, 2) }}</div>
    <div><strong>Restante: ${{ number_format($restante ?? 0, 2) }}</strong></div>
</div>

</body>
</html>