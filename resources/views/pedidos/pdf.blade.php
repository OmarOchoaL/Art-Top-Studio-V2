<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
body{
    font-family: DejaVu Sans;
    font-size:12px;
    color:#222;
}

h2, h4{
    text-align:center;
    margin:0;
}

p{
    margin:4px 0;
}

table{
    width:100%;
    border-collapse: collapse;
    margin-top:10px;
}

td{
    padding:4px;
}

hr{
    margin:8px 0;
}

.total{
    margin-top:10px;
    text-align:right;
    line-height:1.6;
}

.section{
    margin-top:8px;
}
</style>

</head>

<body>

<h2>Art Top Studio</h2>
<h4>Ticket de Pedido</h4>

<p>
<strong>Pedido #:</strong> {{ $pedido->id }} <br>
<strong>Cliente:</strong> {{ $pedido->customer_name ?? 'No especificado' }} <br>
<strong>Teléfono:</strong> {{ $pedido->customer_phone ?? 'Sin teléfono' }} <br>
<strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y') }} <br>
<strong>Hora:</strong> {{ $pedido->created_at->format('h:i A') }} <br>
<strong>Entrega:</strong> {{ $pedido->fecha_entrega ?? 'Sin fecha' }}
</p>

<hr>

@foreach($pedido->items as $item)

<div class="section">
<p>
<strong>{{ $item->product->name }}</strong><br>

Cant: {{ $item->quantity }} |
$ {{ number_format($item->unit_price,2) }} |
Subtotal: ${{ number_format($item->unit_price * $item->quantity,2) }}

@if($item->size)
<br>Tamaño: {{ $item->size }}
@endif

@if($item->color)
<br>Color: {{ $item->color }}
@endif

@if($item->description)
<br>Nota: {{ $item->description }}
@endif

</p>
</div>

@endforeach

<hr>

<div class="total">

<div>Subtotal: ${{ number_format($subtotal,2) }}</div>

<div>Descuento: {{ number_format($descuento,0) }}%</div>

<div>Total: ${{ number_format($total,2) }}</div>

<div>Anticipo: ${{ number_format($anticipo,2) }}</div>

<div><strong>Restante: ${{ number_format($restante,2) }}</strong></div>

</div>

</body>
</html>