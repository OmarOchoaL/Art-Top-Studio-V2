<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderController extends Controller
{
    public function index()
    {
        $pedidos = Order::with('items.product')
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
        'payment_method' => 'nullable|string|max:255',
        'discount' => 'nullable|numeric|min:0|max:100',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'anticipo' => 'nullable|numeric|min:0',
        'fecha_entrega' => 'nullable|date',
    ]);

    $imagenes = [];
    $path = public_path('images/orders');

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
            $nombre = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move($path, $nombre);
            $imagenes[] = $nombre;
        }
    }

    $order = Order::create([
        'customer_name' => $request->customer_name,
        'origin' => $request->origin,
        'status' => 'pendiente',
        'total' => 0,
        'anticipo' => $request->anticipo ?? 0,
        'discount' => $request->discount ?? 0,
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
        'payment_method' => 'nullable|string|max:255',
        'discount' => 'nullable|numeric|min:0|max:100',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'anticipo' => 'nullable|numeric|min:0',
        'fecha_entrega' => 'nullable|date',
    ]);

    $imagenes = is_array($pedido->image) ? $pedido->image : [];
    $path = public_path('images/orders');

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
            $nombre = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move($path, $nombre);
            $imagenes[] = $nombre;
        }
    }

    $pedido->update([
        'customer_name' => $request->customer_name,
        'origin' => $request->origin,
        'anticipo' => $request->anticipo ?? 0,
        'discount' => $request->discount ?? 0,
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
}