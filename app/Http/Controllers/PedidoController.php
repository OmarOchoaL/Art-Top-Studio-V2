<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;

class PedidoController extends Controller
{
    public function index()
{
    $pedidos = \App\Models\Order::with(['items.product', 'address'])
        ->orderBy('id', 'desc')
        ->get();

    return view('pedidos.index', compact('pedidos'));
}

    public function create()
    {
        $productos = Product::all();
        return view('pedidos.create', compact('productos'));
    }

    public function store(Request $request)
{
    $request->validate([
        'origin' => 'required|in:local,online',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:30',
        'payment_method' => 'nullable|string|max:255',
        'discount' => 'nullable|numeric|min:0|max:100',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,jfif|max:2048',
        'anticipo' => 'nullable|numeric|min:0',
        'fecha_entrega' => 'nullable|date',
    ]);
    if (auth()->user()->role === 'colaborador' && (float) $request->discount > 10) {
        return back()
            ->withInput()
            ->with('error', 'El colaborador solo puede aplicar hasta 10% de descuento.');
    }

    $imagenes = [];
    $path = public_path('images/orders');

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
            $nombreOriginal = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nombreOriginal);
            $nombre = $nombreLimpio . '.' . $extension;

            $contador = 1;
            while (file_exists($path . '/' . $nombre)) {
                $nombre = $nombreLimpio . '_' . $contador . '.' . $extension;
                $contador++;
            }

            $file->move($path, $nombre);
            $imagenes[] = $nombre;
        }
    }

    $order = Order::create([
        'user_id' => auth()->id(),
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'origin' => $request->origin,
        'status' => 'pendiente',
        'total' => 0,
        'discount' => $request->discount ?? 0,
        'anticipo' => $request->anticipo ?? 0,
        'fecha_entrega' => $request->fecha_entrega,
        'payment_method' => $request->payment_method,
        'image' => $imagenes,
    ]);

    $subtotal = 0;

    foreach ($request->items as $item) {
        $product = Product::findOrFail($item['product_id']);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'unit_price' => $product->price,
            'size' => $item['size'] ?? null,
            'color' => $item['color'] ?? null,
            'description' => $item['description'] ?? null,
        ]);

        $subtotal += $product->price * $item['quantity'];
    }

    $descuento = (float) ($request->discount ?? 0);
    $total = $subtotal - ($subtotal * $descuento / 100);

    $order->update([
        'total' => $total,
        'discount' => $descuento,
    ]);

    return redirect()->route('pedidos.index')
        ->with('success', 'Pedido creado correctamente');
}

    public function show($id)
    {
        $pedido = Order::with('items.product')->findOrFail($id);
        return view('pedidos.show', compact('pedido'));
    }

    public function edit($id)
    {
        $pedido = Order::with('items')->findOrFail($id);
        $productos = Product::all();

        return view('pedidos.edit', compact('pedido', 'productos'));
    }

    public function update(Request $request, $id)
{
    $pedido = Order::with('items')->findOrFail($id);

    $request->validate([
        'origin' => 'required|in:local,online',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:30',
        'payment_method' => 'nullable|string|max:255',
        'discount' => 'nullable|numeric|min:0|max:100',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,jfif|max:2048',
        'anticipo' => 'nullable|numeric|min:0',
        'fecha_entrega' => 'nullable|date',
    ]);

    if (auth()->user()->role === 'colaborador' && (float) $request->discount > 10) {
        return back()
            ->withInput()
            ->with('error', 'El colaborador solo puede aplicar hasta 10% de descuento.');
    }

    $imagenes = is_array($pedido->image) ? $pedido->image : [];
    $path = public_path('images/orders');

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
            $nombreOriginal = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nombreOriginal);
            $nombre = $nombreLimpio . '.' . $extension;

            $contador = 1;
            while (file_exists($path . '/' . $nombre)) {
                $nombre = $nombreLimpio . '_' . $contador . '.' . $extension;
                $contador++;
            }

            $file->move($path, $nombre);
            $imagenes[] = $nombre;
        }
    }

    $pedido->update([
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'origin' => $request->origin,
        'discount' => $request->discount ?? 0,
        'anticipo' => $request->anticipo ?? 0,
        'fecha_entrega' => $request->fecha_entrega,
        'payment_method' => $request->payment_method,
        'image' => $imagenes,
    ]);

    $pedido->items()->delete();

    $subtotal = 0;

    foreach ($request->items as $item) {
        $product = Product::findOrFail($item['product_id']);

        OrderItem::create([
            'order_id' => $pedido->id,
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'unit_price' => $product->price,
            'size' => $item['size'] ?? null,
            'color' => $item['color'] ?? null,
            'description' => $item['description'] ?? null,
        ]);

        $subtotal += $product->price * $item['quantity'];
    }

    $descuento = (float) ($request->discount ?? 0);
    $total = $subtotal - ($subtotal * $descuento / 100);

    $pedido->update([
        'total' => $total,
        'discount' => $descuento,
    ]);

    return redirect()->route('pedidos.index')
        ->with('success', 'Pedido actualizado correctamente');
}

    public function destroy(Request $request, $id)
{
    if (auth()->user()->role !== 'admin') {
        return back()->with('error', 'No tienes permiso para eliminar pedidos.');
    }
    $pedido = Order::with('items')->findOrFail($id);

    $request->validate([
        'admin_password' => 'required|string',
    ]);

    if (!\Illuminate\Support\Facades\Hash::check($request->admin_password, auth()->user()->password)) {
        return back()->with('error', 'La contraseña del administrador es incorrecta.');
    }

    $pedido->items()->delete();
    $pedido->delete();

    return redirect()->route('pedidos.index')
        ->with('success', 'Pedido eliminado correctamente');
}

    public function cambiarEstado(Request $request, $id)
    {
        $pedido = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $pedido->update([
            'status' => $request->status,
        ]);

        return redirect()->route('pedidos.index')
            ->with('success', 'Estado actualizado correctamente');
    }

    public function cotizar(Request $request)
{
    $request->validate([
        'origin' => 'required|in:local,online',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:30', 
        'payment_method' => 'nullable|string|max:255',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'anticipo' => 'nullable|numeric|min:0',
        'fecha_entrega' => 'nullable|date',
    ]);

    $itemsCotizacion = [];
    $subtotal = 0;

    foreach ($request->items as $item) {
        $product = Product::findOrFail($item['product_id']);
        $importe = $product->price * $item['quantity'];

        $itemsCotizacion[] = [
            'producto' => $product->name,
            'cantidad' => $item['quantity'],
            'precio' => $product->price,
            'importe' => $importe,
            'size' => $item['size'] ?? null,
            'color' => $item['color'] ?? null,
            'description' => $item['description'] ?? null,
        ];

        $subtotal += $importe;
    }

    $descuento = (float) ($request->discount ?? 0);
    $total = $subtotal - ($subtotal * $descuento / 100);
    $anticipo = (float) ($request->anticipo ?? 0);
    $restante = $total - $anticipo;

    $data = [
    'customer_name' => $request->customer_name,
    'customer_phone' => $request->customer_phone, 
    'origin' => $request->origin,
    'payment_method' => $request->payment_method,
    'fecha_entrega' => $request->fecha_entrega,
    'anticipo' => $anticipo,
    'descuento' => $descuento,
    'subtotal' => $subtotal,
    'total' => $total,
    'restante' => $restante,
    'items' => $itemsCotizacion,

    'generado_por_nombre' => auth()->user()->name ?? 'No especificado',
    'generado_por_rol' => auth()->user()->role ?? 'No especificado',
];

    $pdf = Pdf::loadView('pedidos.cotizar', $data)->setPaper('a4', 'portrait');

    return $pdf->stream('cotizacion.pdf');
}

    public function pdf($id)
{
    $pedido = Order::with('items.product')->findOrFail($id);

    $subtotal = 0;

    foreach ($pedido->items as $item) {
        $subtotal += $item->unit_price * $item->quantity;
    }

    $descuento = $pedido->discount ?? 0;
    $total = $subtotal - ($subtotal * $descuento / 100);
    $anticipo = $pedido->anticipo ?? 0;
    $restante = $total - $anticipo;

    $pdf = Pdf::loadView('pedidos.pdf', [
        'pedido' => $pedido,
        'subtotal' => $subtotal,
        'descuento' => $descuento,
        'total' => $total,
        'anticipo' => $anticipo,
        'restante' => $restante,
    ])->setPaper('a4', 'portrait');

    return $pdf->stream('ticket-pedido-'.$pedido->id.'.pdf');
}

public function pdfEntrega($id)
{
    $pedido = \App\Models\Order::with(['items.product', 'address', 'user'])->findOrFail($id);

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pedidos.pdf_entrega', compact('pedido'));

    return $pdf->stream('pedido-entrega-' . $pedido->id . '.pdf');
}
}